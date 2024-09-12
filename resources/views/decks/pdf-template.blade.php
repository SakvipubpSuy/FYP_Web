<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        td {
            width: 33%; /* Each QR code will take 1/3 of the row */
            text-align: center;
            padding: 20px;
            vertical-align: top;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .card-name {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>{{ $decks->deck_name }} QR Codes</h1>
    <table>
        <tr>
        @foreach($cards as $index => $card)
            <td>
                <div class="card-name">{{ $card->card_name }}</div>
                @if($card->qr_code_base64)
                    <img src="{{ $card->qr_code_base64 }}" alt="QR Code">
                @else
                    <p>No QR Code Available</p>
                @endif
            </td>
            @if(($index + 1) % 3 == 0)  <!-- Wrap row after every 3rd item -->
        </tr><tr>
            @endif
        @endforeach
        </tr>
    </table>
</body>
</html>
