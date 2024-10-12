<x-mail::message>
    #Order Placed Succesfully

    Thank you for order. Dengan ID ORder : {{ $order->id }}.

    The body of your message.

    <x-mail::button :url="$url">
        Lihat Detail Orderan Anda
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
