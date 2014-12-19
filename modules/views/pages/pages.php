Test page
<?php

$data['query'] = '[[module type=test name=aaa]][[@a]]';
$data = Loader::gi()->getLQ($data);
//echo $data['query'];

?>