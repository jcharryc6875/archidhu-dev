$shareLink = "https://api.onedrive.com/v1.0/shares/u!bb3810083dd6cecd/root/content"
$downloadUrl = "https://company.sharepoint.com/sites/site/_layouts/15/download.aspx?SourceUrl=/sites/site/Shared Documents/image.jpg"
$folderPath = "C:\TEMP"
$localPath = Join-Path $folderPath "Image.jpg"

# Paso 2: Asegurarse de que la carpeta exista
if (-not (Test-Path -Path $folderPath -PathType Container)) {
    New-Item -Path $folderPath -ItemType Directory -Force | Out-Null
}

# Paso 3: Crear sesión para guardar las cookies
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# Paso 4: Petición inicial para agarrar las cookies
Invoke-WebRequest -Uri $shareLink -WebSession $session -MaximumRedirection 0 -UseBasicParsing -ErrorAction SilentlyContinue

# Paso 5: Usar la cookie para bajar el archivo
Invoke-WebRequest -Uri $downloadUrl -WebSession $session -OutFile $localPath -UseBasicParsing