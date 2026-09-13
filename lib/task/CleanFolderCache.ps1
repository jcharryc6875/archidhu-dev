# set folder path
$dump_path = "C:\inetpub\wwwroot\sgdeapruebas\cache"

# delete the files
Get-ChildItem $dump_path -Recurse | Remove-Item -Recurse
# delete the folders
#Get-ChildItem $dump_path -Directory -Recurse | Remove-Item -Recurse