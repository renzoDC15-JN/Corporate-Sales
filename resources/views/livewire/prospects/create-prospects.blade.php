<div class="flex justify-center items-center min-h-screen">
    <div class="w-full max-w-lg bg-white p-4 rounded-lg">
        <div class=" flex justify-center mb-4">
            <img class="h-auto w-40 lg:w-1/2" src="CompanyLogo.png" alt="CompanyLogo.png">
        </div>
        <form wire:submit="create" class="w-full">

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
            <table class="table-auto w-full">
                <tbody>
                <tr class="border-b">
                    <td class="px-4 py-2">First Name</td>
                    <td class="px-4 py-2">{{ $data['first_name'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Middle Name</td>
                    <td class="px-4 py-2">{{ $data['middle_name'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Last Name</td>
                    <td class="px-4 py-2">{{ $data['last_name'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Company</td>
                    <td class="px-4 py-2">{{ $data['company'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Position/Title</td>
                    <td class="px-4 py-2">{{ $data['position_title'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Gross Monthly Salary</td>
                    <td class="px-4 py-2">{{ $data['salary'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">PAG-IBIG Number / MID Number</td>
                    <td class="px-4 py-2">{{ $data['mid'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Mobile Number</td>
                    <td class="px-4 py-2">{{ $data['mobile_number'] ?? '' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2">Email</td>
                    <td class="px-4 py-2">{{ $data['email'] ?? '' }}</td>
                </tr>
                </tbody>
            </table>

        </div>
    </x-filament::modal>

</div>
