<?php
session_start();
require_once 'includes/connection.php';

    $id = $_GET['rejectedid'];
    
 

    

       
           
            $Status = "rejected";

            $Update_query = "UPDATE `tbl_apply_student` SET `status` = '$Status' WHERE `tbl_apply_student`.`id` = '$id'";
            $result = mysqli_query($conn,$Update_query);



            $Select_query = "SELECT * FROM `tbl_apply_student` WHERE id = '$id' ";
            $result = mysqli_query($conn,$Select_query);
            $call = mysqli_fetch_assoc($result);












                                
                    use Infobip\Configuration;
                    use Infobip\Api\SmsApi;
                    use Infobip\Model\SmsDestination;
                    use Infobip\Model\SmsTextualMessage;
                    use Infobip\Model\SmsAdvancedTextualRequest;
                    use Twilio\Rest\Client;
                    

                    require __DIR__ . "/vendor/autoload.php";

                    $number = "+250".$call['Contact'];
                    $message ="Giheke TVET School Message: \n\n"."Hi ". $call['LastName'].",\n "."Sorry  You Failed  For School Application.\n For More Information Contact Us " ;

                   



                        $base_url = "https://pp19g8.api.infobip.com";
                        $api_key = "fc9634fa860b0245da72c4a904ff71fa-0a1584bc-fca9-413a-9a90-a313dd6d9df1";



                        $configuration = new Configuration(host: $base_url, apiKey: $api_key);

                        $api = new SmsApi(config: $configuration);

                        $destination = new SmsDestination(to: $number);

                        $message = new SmsTextualMessage(
                            destinations: [$destination],
                            text: $message,
                            from: "TVET"
                        );

                        $request = new SmsAdvancedTextualRequest(messages: [$message]);

                        $response = $api->sendSmsMessage($request);









































                        
            require_once __DIR__ . '/../includes/smtp-config.php';

        try {
            $mail = getMailer();
            $mail->addAddress($call['Email'], $call['FirstName'] . ' ' . $call['LastName']);
            $mail->Subject = 'Giheke TSS School - Application Update';
            $mail->Body = '<h3>Dear ' . htmlspecialchars($call['FirstName'] . ' ' . $call['LastName']) . ',</h3>'
                . '<p>Thank you for your interest in GIHEKE Technical Secondary School.</p>'
                . '<p>After careful review, we regret to inform you that your application has not been approved at this time.</p>'
                . '<p>For more information, please contact us at giheketss@gmail.com or call +250 788 876 460.</p>'
                . '<br><p>Best regards,<br>GIHEKE TSS Administration</p>';
            $mail->send();
        } catch (Exception $e) {
            error_log('Rejection email failed: ' . $e->getMessage());
        }

        

        header('location:studentApplication.php?error=Student Rejected Successfully');
           





            




