<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testuen itzultzailea</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header svg {
            width: 40px;
            height: 40px;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            background-color: white;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            margin-bottom: 20px;
        }

        .messages-area {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: #f9f9f9;
        }

        .message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 8px;
            line-height: 1.4;
            font-size: 0.95rem;
            position: relative;
            word-wrap: break-word;
        }

        .message.user {
            align-self: flex-end;
            background-color: #007bff;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .message.assistant {
            align-self: flex-start;
            background-color: #e0e0e0;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .message.system {
            align-self: center;
            background-color: #ffeeba;
            color: #856404;
            font-size: 0.85rem;
            max-width: 90%;
            text-align: center;
        }

        .input-area {
            padding: 15px;
            background-color: #ffffff;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .input-area input {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-area select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            background-color: white;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .input-area input:focus,
        .input-area select:focus {
            border-color: #007bff;
        }

        .input-area button {
            background-color: #0084ff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .input-area button:hover {
            background-color: #006bcf;
        }

        /* Scrollbar styling */
        .messages-area::-webkit-scrollbar {
            width: 8px;
        }

        .messages-area::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .messages-area::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .messages-area::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
    </style>
</head>

<body>

    <div class="header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 8l6 6"></path>
            <path d="M4 14h6"></path>
            <path d="M2 5h12"></path>
            <path d="M7 2h1"></path>
            <path d="M22 22l-5-10-5 10"></path>
            <path d="M14 18h6"></path>
        </svg>
        <h1>Testuen itzultzailea</h1>
    </div>

    <div class="chat-container">
        <div class="messages-area">
            {{-- Display errors --}}
            @if ($errors->any())
                <div class="message system">
                    Mesedez, zuzendu ondorengo akatsak:
                    @foreach ($errors->all() as $error)
                        <br>{{ $error }}
                    @endforeach
                </div>
            @endif

            {{-- Display system messages like exceptions --}}
            @if (session('error'))
                <div class="message system">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Display the input text if it exists (repopulating) --}}
            @if (old('testua'))
                <div class="message user">
                    {{ old('testua') }}
                </div>
            @elseif (isset($testua)) 
                <div class="message user">
                    {{ $testua }}
                </div>
            @endif

            {{-- Display the translation result --}}
            @if (isset($translation))
                <div class="message assistant">
                    {!! $translation !!}
                </div>
            @endif
        </div>

        <form action="{{ url('/') }}" method="POST" class="input-area">
            @csrf
            <input type="text" name="testua" placeholder="Idatzi testua hemen..." value="{{ old('testua', $testua ?? '') }}" autocomplete="off">

            <select name="hizkuntza">
                <option value="" disabled {{ old('hizkuntza', $hizkuntza ?? '') ? '' : 'selected' }}>Hizkuntza</option>
                @foreach ($hizkuntzak as $key => $label)
                    <option value="{{ $key }}" {{ old('hizkuntza', $hizkuntza ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit">Itzuli</button>
        </form>
    </div>

</body>

</html>