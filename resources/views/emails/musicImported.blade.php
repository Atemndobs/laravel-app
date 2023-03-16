@component('mail::message')
# New Music Import Notification

New music has  been Imported

@component('mail::button', ['url' => 'http://mage.tech:7700/'])
Click here to view it
@endcomponent

Thanks,<br>
Atem from {{ config('app.name') }}
@endcomponent
