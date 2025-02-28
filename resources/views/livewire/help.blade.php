<div class="flex flex-col w-11/12 max-w-5xl gap-2 modal-box">
    <h3>How can we help?</h3>
    <div>Your feedback helps us to improve Coolify. Thank you! 💜</div>
    <form wire:submit="submit" class="flex flex-col gap-4 pt-4">
        <x-forms.input id="subject" label="Subject" placeholder="Summary of your problem."></x-forms.input>
        <x-forms.textarea rows="10" id="description" label="Description"
            placeholder="Please provide as much information as possible."></x-forms.textarea>
        <div></div>
        <x-forms.button class="w-full mt-4" type="submit" onclick="help.close()">Send</x-forms.button>
    </form>
</div>
