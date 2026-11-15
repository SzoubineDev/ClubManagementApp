<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de salle</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            line-height: 1.55;
            color: #111;
            margin: 2.5cm 2.2cm;
        }

        .date {
            text-align: right;
            margin-bottom: 28px;
        }

        .recipient {
            margin-bottom: 28px;
        }

        .object {
            font-weight: bold;
            margin: 24px 0;
        }

        ul {
            margin: 12px 0 20px 24px;
        }

        .signature {
            margin-top: 48px;
            text-align: right;
        }

        .muted {
            color: #444;
        }
    </style>
</head>
<body>

    <p class="date">Le {{ $today }}</p>

    <div class="recipient">
        <p>
            À Monsieur le Doyen de la Faculté<br>
            <span class="muted">Objet : Demande de mise à disposition d'une salle</span>
        </p>
    </div>

    <p class="object">Objet : Demande de salle</p>

    <p>Monsieur le Doyen,</p>

    <p>
        Au nom du club <strong>The Great Debaters</strong>, j'ai l'honneur de solliciter
        votre haute bienveillance afin de mettre à notre disposition une salle pour
        l'activité suivante :
    </p>

    <ul>
        <li><strong>Salle souhaitée :</strong> {{ $room ?: '…………………………………………' }}</li>
        <li><strong>Date :</strong> {{ $requestedDate ?: '…………………………………………' }}</li>
        <li><strong>Heure :</strong> {{ $time ?: '…………………………………………' }}</li>
        <li><strong>Objet :</strong> {{ $purpose ?: '…………………………………………' }}</li>
    </ul>

    <p>
        Cette salle nous permettra d'organiser nos séances d'entraînement et de
        préparation aux débats dans de bonnes conditions. Nous nous engageons à
        respecter le règlement intérieur de l'établissement et à laisser la salle
        dans son état initial après utilisation.
    </p>

    <p>
        Dans l'attente d'une réponse favorable, je vous prie d'agréer, Monsieur le
        Doyen, l'expression de ma haute considération.
    </p>

    <div class="signature">
        <p>
            <strong>{{ $requester->name }}</strong><br>
            Assistant Général — The Great Debaters<br>
            {{ $requester->email }}
        </p>
    </div>

</body>
</html>
