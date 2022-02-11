# auto-delete
Commercers_AutoDelete is an extension auto delete folder or file after a certain time

# Configuration:
- Directory: Admin Panel -> System -> Configuration -> COMMERCERS -> Auto Delete.

1. Set day delete folder
ex: 
- /report|5 (will delete /root/var/report after 5 day)
- /cache|1 (will delete /root/var/cache after 1 day)

2. Set size delete file
ex:
- /log/connector.log|0|1000000 (will delete /root/var/log/connector.log if file connector.log > 1000000 Bytes)
- /log/exception.log|0|1000000 (will delete /root/var/log/connector.log if file connector.log > 1000000 Bytes)
- /log/system.log|0|1000000
- /log/debug.log|0|1000000
