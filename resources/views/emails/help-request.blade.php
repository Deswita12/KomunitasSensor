<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2 style="color:#416744;">Pesan Bantuan Baru</h2>
    <p><strong>Nama:</strong> {{ $helpRequest->name }}</p>
    <p><strong>Email:</strong> {{ $helpRequest->email }}</p>
    <p><strong>Pesan:</strong></p>
    <p>{{ $helpRequest->message }}</p>
    <hr>
    <p style="font-size: 12px; color: #888;">
        Dikirim dari form Bantuan SensorKita pada {{ $helpRequest->created_at->format('d M Y H:i') }}
    </p>
</body>
</html>