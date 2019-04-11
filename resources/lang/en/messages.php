<?php

return [
    'aws_wasnot_updated_automatically' =>
<<<STR
AWS Instance ID have not been updated automatically after changing a LiveSwitch url.
It may happen by one of following reason:
 1) We can not identify a host of LS url. Maybe is protocol (http/https) missed?
 2) We can not get IP of LS host.
 3) Error have happened during retrieve AWS instances. Maybe are/were others aws settings wrong?
 4) You don't have an AWS Instance with LS's ip. Keep in mind, we can not retrieve an IP for stopped instances.
STR
];
