<div x-init="$wire.getLogs">
    <div class="flex gap-2">
        <h4>Container: {{ $container }}</h4>
        @if ($streamLogs)
            <span wire:poll.2000ms='getLogs(true)' class="loading loading-xs text-warning loading-spinner"></span>
        @endif
    </div>
    <div class="flex gap-2">
        <x-forms.checkbox instantSave label="Stream Logs" id="streamLogs"></x-forms.checkbox>
        <x-forms.checkbox instantSave label="Include Timestamps" id="showTimeStamps"></x-forms.checkbox>
    </div>
    <form wire:submit='getLogs(true)' class="flex items-end gap-2">
        <x-forms.input label="Only Show Number of Lines" placeholder="1000" required id="numberOfLines"></x-forms.input>
        <x-forms.button type="submit">Refresh</x-forms.button>
    </form>
    <div id="screen" x-data="{ fullscreen: false, alwaysScroll: false, intervalId: null }" :class="fullscreen ? 'fullscreen' : 'w-full py-4 mx-auto'">
        <div class="relative flex flex-col-reverse w-full p-4 pt-6 overflow-y-auto text-white bg-coolgray-100 scrollbar border-coolgray-300"
            :class="fullscreen ? '' : 'max-h-[40rem] border border-solid rounded'">
            <button title="Minimize" x-show="fullscreen" class="fixed top-4 right-4" x-on:click="makeFullscreen"><svg
                    class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M6 14h4m0 0v4m0-4l-6 6m14-10h-4m0 0V6m0 4l6-6" />
                </svg></button>
            <button title="Go Top" x-show="fullscreen" class="fixed top-4 right-28" x-on:click="goTop"> <svg
                    class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 5v14m4-10l-4-4M8 9l4-4" />
                </svg></button>
            <button title="Follow Logs" x-show="fullscreen" :class="alwaysScroll ? 'text-warning' : ''"
                class="fixed top-4 right-16" x-on:click="toggleScroll"><svg class="icon" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M12 5v14m4-4l-4 4m-4-4l4 4" />
                </svg></button>

            <button title="Fullscreen" x-show="!fullscreen" class="absolute top-2 right-2"
                x-on:click="makeFullscreen"><svg class=" icon" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <g fill="none">
                        <path
                            d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z" />
                        <path fill="currentColor"
                            d="M9.793 12.793a1 1 0 0 1 1.497 1.32l-.083.094L6.414 19H9a1 1 0 0 1 .117 1.993L9 21H4a1 1 0 0 1-.993-.883L3 20v-5a1 1 0 0 1 1.993-.117L5 15v2.586l4.793-4.793ZM20 3a1 1 0 0 1 .993.883L21 4v5a1 1 0 0 1-1.993.117L19 9V6.414l-4.793 4.793a1 1 0 0 1-1.497-1.32l.083-.094L17.586 5H15a1 1 0 0 1-.117-1.993L15 3h5Z" />
                    </g>
                </svg></button>
            <pre id="logs" class="font-mono whitespace-pre-wrap">{{ $outputs }}</pre>
        </div>
    </div>
    <script>
        function makeFullscreen() {
            this.fullscreen = !this.fullscreen;
            if (this.fullscreen === false) {
                this.alwaysScroll = false;
                clearInterval(this.intervalId);
            }
        }

        function toggleScroll() {
            this.alwaysScroll = !this.alwaysScroll;

            if (this.alwaysScroll) {
                this.intervalId = setInterval(() => {
                    const screen = document.getElementById('screen');
                    const logs = document.getElementById('logs');
                    if (screen.scrollTop !== logs.scrollHeight) {
                        screen.scrollTop = logs.scrollHeight;
                    }
                }, 100);
            } else {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        }

        function goTop() {
            this.alwaysScroll = false;
            clearInterval(this.intervalId);
            const screen = document.getElementById('screen');
            screen.scrollTop = 0;
        }
    </script>
</div>
