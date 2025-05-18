<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-dyZt7pGKVyhlHLLJoPLD114F8CbnMD4PlzyBbs6k8ZZrVSu2VvulaHYodEc/WWEDuJeXQMZT6X0hgqP/d9vywg==" crossorigin="anonymous" referrerpolicy="no-referrer" />




@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
