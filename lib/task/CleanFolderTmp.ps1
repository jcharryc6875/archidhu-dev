# set folder path
$dump_path = "C:\inetpub\wwwroot\sgdeapruebas\web\tmp"

# set min age of files
$max_days = "-1"
$max_hours = "-4"
 
# get the current date
$curr_date = Get-Date

# determine how far back we go based on current date
$del_date = $curr_date.AddDays($max_days)
$del_hours = $curr_date.Addhours($max_days)

# delete the files
Get-ChildItem $dump_path -Recurse | Where-Object { $_.LastWriteTime -lt $del_date } | Remove-Item -Recurse
# delete the folders
Get-ChildItem $dump_path -Directory -Recurse | Where-Object { $_.LastWriteTime -lt $del_date } | Remove-Item -Recurse

# delete the files by hours
Get-ChildItem $dump_path -Recurse | Where-Object { $_.LastWriteTime -lt $del_hours } | Remove-Item -Recurse
# delete the folders by hours
Get-ChildItem $dump_path -Directory -Recurse | Where-Object { $_.LastWriteTime -lt $del_hours } | Remove-Item -Recurse