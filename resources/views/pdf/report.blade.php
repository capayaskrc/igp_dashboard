<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] }}</title>
    <!-- Include any additional CSS stylesheets or meta tags here -->
</head>
<body>
<h1>{{ $data['title'] }}</h1>
<p>{{ $data['content'] }}</p>

<!-- Example table -->
<table border="1">
    <tr>
        <th>Column 1</th>
        <th>Column 2</th>
        <th>Column 3</th>
    </tr>
    <tr>
        <td>Data 1</td>
        <td>Data 2</td>
        <td>Data 3</td>
    </tr>
    <!-- Add more rows as needed -->
</table>

<!-- Example image -->
<img src="{{ asset('path/to/image.jpg') }}" alt="Image Description">
</body>
</html>
