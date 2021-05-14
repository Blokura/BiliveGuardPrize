<?php
setcookie("social_uid", "", time() - 604800, '/');
exit('<script>window.location.href="index.php"</script>');