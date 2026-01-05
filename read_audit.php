<?php

$content = file_get_contents('software_audit_full.txt');
// Handle encoding if needed, but file_get_contents usually fine for binary read.
// We can just dump it.
echo $content;
