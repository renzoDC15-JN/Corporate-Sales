<div class="flex justify-center items-center min-h-screen">
    <div class="w-full bg-white p-4 rounded-lg mx-10">
        <div class=" flex justify-center mb-4">
            <img class="h-auto w-40 lg:w-1/4" src="/CompanyLogo.png" alt="CompanyLogo.png">
        </div>
        <form wire:submit="save" class="w-full">
            {{ $this->form }}
            <div class="flex justify-center ">
                <x-filament::button type="submit" class="mt-4  text-white py-2 px-4 rounded mx-auto w-60">
                    Submit
                </x-filament::button>
            </div>
        </form>
        <x-filament-actions::modals />

    </div>
    <x-filament::modal
        id="success-modal"
        icon="heroicon-o-check-circle"
        icon-color="success"
        sticky-header
        width="md"
        class="rounded-md"
        :autofocus="false"
        x-on:close-modal.window="$wire.closeModal()">
        <x-slot name="heading">
            Registration Successful
        </x-slot>
        <x-slot name="description">
            Thank you for registering!
        </x-slot>
        <div class="px-4 py-2">

        </div>
    </x-filament::modal>

</div>
<script>
    function updateScreenSize() {
        const screenWidth = window.innerWidth;
        let screenSize = 'desktop';

        if (screenWidth < 768) {
            screenSize = 'mobile';
        } else if (screenWidth >= 768 && screenWidth < 1024) {
            screenSize = 'md';
        }

        console.log(`Screen size is: ${screenSize}`);

        // Dispatch the event using $wire if Livewire is loaded
        if (window.Livewire) {
            this.$wire.dispatchSelf('screenSizeUpdated', { screenSize });
        }
    }

    // Normal document load
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Document is fully loaded');
        updateScreenSize(); // Trigger screen size detection on normal document load
    });

</script>
