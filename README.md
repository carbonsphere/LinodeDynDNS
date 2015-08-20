Author  : CarbonSphere</br>
Email   : CarbonSphere@gmail.com</br>

# Linode dynamic ip updater for DNS records 

  1. Edit LinodeDynDNS.php

  Add the following arguments 
    - DOMAINID     //Domain ID can be left empty and run through script to obtain.
    - RESOURCEID   //Resource ID can be left empty and run through script to obtain.
    - KEY          //Must obtain application key from Linode.

> php LinodeDynDNS.php {domain id} {resource id}

If you don't know your domain id and resource id, then run the command like following.

> php LinodeDynDNS.php

```
Error: Please obtain your Domain ID first
Listing Domains:
Domain: example.com
Domain ID: 123450
Run the command again with your domain ID
```

> php LinodeDynDNS.php 123450

```
Error: Please obtain your resource ID first
Listing resources
Name: www
Resource ID: 2233333

Name: carbon
Resource ID: 2233334

Name: ns1
Resource ID: 2233335

Run the command again with your Resource ID
```

Update "www.example.com" ip address

> php LinodeDynDNS.php 123450 2233333

```
Your public IP = 192.111.222.1
Update Complete
```

Domain ID and Resource ID can be inserted into LinodeDynDNS.php so you don't have to run it with arguments in cron.

