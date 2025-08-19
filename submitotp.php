<?php$chechotp=$_POST['chechotp'];
$otp=$_POST['otp'];

if($checkotp==$otp){
    echo "Otp is Verified";
}else{
    echo "Incorrect Otp"
}
?>