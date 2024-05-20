<?php
session_start();
session_destroy();
echo "Your session is destroyed<br>";
echo "<a href='index.php'><<-- Go back to the login.</a>";