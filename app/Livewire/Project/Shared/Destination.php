<?php

namespace App\Livewire\Project\Shared;

use App\Actions\Application\StopApplicationOneServer;
use App\Events\ApplicationStatusChanged;
use App\Models\Server;
use App\Models\StandaloneDocker;
use Livewire\Component;
use Visus\Cuid2\Cuid2;

class Destination extends Component
{
    public $resource;
    public $networks = [];

    public function getListeners()
    {
        $teamId = auth()->user()->currentTeam()->id;
        return [
            "echo-private:team.{$teamId},ApplicationStatusChanged" => 'loadData',
        ];
    }
    public function mount()
    {
        $this->loadData();
    }
    public function loadData()
    {
        $all_networks = collect([]);
        $all_networks = $all_networks->push($this->resource->destination);
        $all_networks = $all_networks->merge($this->resource->additional_networks);

        $this->networks = Server::isUsable()->get()->map(function ($server) {
            return $server->standaloneDockers;
        })->flatten();
        $this->networks = $this->networks->reject(function ($network) use ($all_networks) {
            return $all_networks->pluck('id')->contains($network->id);
        });
        $this->networks = $this->networks->reject(function ($network) {
            return $this->resource->destination->server->id == $network->server->id;
        });
    }
    public function redeploy(int $network_id, int $server_id)
    {
        if ($this->resource->additional_servers->count() > 0 && str($this->resource->docker_registry_image_name)->isEmpty()) {
            $this->dispatch('error', 'Failed to deploy.', 'Before deploying to multiple servers, you must first set a Docker image in the General tab.<br>More information here: <a target="_blank" class="underline" href="https://coolify.io/docs/server/multiple-servers">documentation</a>');
            return;
        }
        $deployment_uuid = new Cuid2(7);
        $server = Server::find($server_id);
        $destination = StandaloneDocker::find($network_id);
        queue_application_deployment(
            deployment_uuid: $deployment_uuid,
            application: $this->resource,
            server: $server,
            destination: $destination,
            no_questions_asked: true,
        );
        return redirect()->route('project.application.deployment.show', [
            'project_uuid' => data_get($this->resource, 'environment.project.uuid'),
            'application_uuid' => data_get($this->resource, 'uuid'),
            'deployment_uuid' => $deployment_uuid,
            'environment_name' => data_get($this->resource, 'environment.name'),
        ]);
    }
    public function addServer(int $network_id, int $server_id)
    {
        $this->resource->additional_networks()->attach($network_id, ['server_id' => $server_id]);
        $this->resource->load(['additional_networks']);
        ApplicationStatusChanged::dispatch(data_get($this->resource, 'environment.project.team.id'));
        $this->loadData();
    }
    public function removeServer(int $network_id, int $server_id)
    {
        if ($this->resource->destination->server->id == $server_id && $this->resource->destination->id == $network_id) {
            $this->dispatch('error', 'You cannot remove this destination server.', 'You are trying to remove the main server.');
            return;
        }
        $server = Server::find($server_id);
        StopApplicationOneServer::run($this->resource, $server);
        $this->resource->additional_networks()->detach($network_id, ['server_id' => $server_id]);
        $this->resource->load(['additional_networks']);
        ApplicationStatusChanged::dispatch(data_get($this->resource, 'environment.project.team.id'));
        $this->loadData();
    }
}
