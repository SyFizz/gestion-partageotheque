@component('mail::message')
    # Votre compte a été validé

    Bonjour {{ $user->name }},

    Votre compte sur la plateforme de La Partageothèque a été validé. Vous pouvez dès à présent vous connecter et utiliser nos services.

    @component('mail::button', ['url' => route('login')])
        Se connecter
    @endcomponent

    Merci,<br>
    L'équipe de La Partageothèque
@endcomponent
