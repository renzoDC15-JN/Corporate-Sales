<div class="flex justify-center items-center min-h-screen">
    <div class="w-full bg-white p-4 rounded-lg mx-10">
        <div class=" flex justify-center mb-4">
            <img class="h-auto w-full lg:w-1/2 " src="/CompanyLogo.png" alt="CompanyLogo.png">
        </div>
            <form wire:submit="save" class="w-full">
                <div  class="flex justify-center ">
                    <h2 class="text-xl font-bold leading-tight text-gray-800 mb-4">
                        {{$this->company}}
                    </h2>
                </div>
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
        :close-button="false"
        :close-by-escaping="false"
        :close-by-clicking-away="false">
        <x-slot name="heading">
            Client Information Sheet
        </x-slot>
        <x-slot name="description">
            Thank you for completing this form!
        </x-slot>
        <div class="px-4 py-2">
            Please check your email for additional instructions
        </div>
    </x-filament::modal>

</div>
<script>

    function updateScreenSize() {
        const screenWidth = window.innerWidth;
        let screenSize = 'desktop';

        if (screenWidth < 769) {
            screenSize = 'mobile';
        } else if (screenWidth >= 769 && screenWidth < 1024) {
            screenSize = 'md';
        }
        @this.set('screenSize',screenSize);
    }

    // Normal document load
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            updateScreenSize(); // Trigger screen size detection after 200ms delay
        }, 50);
        {{--if (@js($has_data)) {--}}
        {{--    setTimeout(() => {--}}
        {{--        // Open the modal using Filament's modal manager--}}
        {{--        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'hasdata-modal' }}));--}}
        {{--    }, 200); // Delay by 200ms, adjust as necessary--}}
        {{--}--}}
    });

    window.addEventListener('resize', function(event) {
        updateScreenSize();
    }, true);

</script>
