```blade
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
</head>

<body>

    <h1>Contact Form</h1>

    <form method="POST" action="/form">
        @csrf

        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>

        <button type="submit">Submit</button>
    </form>

</body>

</html>
```