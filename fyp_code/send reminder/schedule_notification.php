<?php
function sendVetAppointmentEmails($connect, $vetid) {
    // Fetch the vet's email address based on the vetid
    $vetEmailQuery = "SELECT email FROM vet WHERE vetid = ?";
    $vetEmail = null;

    if ($stmt = mysqli_prepare($connect, $vetEmailQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $vetid);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $vetEmail);
            mysqli_stmt_fetch($stmt);
        }
        mysqli_stmt_close($stmt);
    }

    // If no email found, exit the function
    if (!$vetEmail) {
        error_log("No email found for vetid: $vetid");
        return;
    }

    // Fetch all "occupied" schedules for the vet that are upcoming
    $occupiedScheduleQuery = "
        SELECT appid, appname, datetime, role, scheduledstatus
        FROM schedule
        WHERE vetid = ? AND scheduledstatus = 'occupied' AND datetime >= NOW()
        ORDER BY datetime
    ";

    $occupiedSchedules = [];
    if ($stmt = mysqli_prepare($connect, $occupiedScheduleQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $vetid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $occupiedSchedules[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }

    // If no schedules found, exit the function
    if (empty($occupiedSchedules)) {
        error_log("No upcoming occupied schedules for vetid: $vetid");
        return;
    }

    // Send email for each upcoming schedule
    foreach ($occupiedSchedules as $schedule) {
        $subject = "Upcoming Appointment Notification: " . htmlspecialchars($schedule['appname']);
        $message = "
            <html>
            <head>
                <title>Upcoming Appointment Notification</title>
            </head>
            <body>
                <h2>Dear Veterinarian,</h2>
                <p>You have an upcoming appointment with the following details:</p>
                <ul>
                    <li><strong>Appointment Name:</strong> " . htmlspecialchars($schedule['appname']) . "</li>
                    <li><strong>Date:</strong> " . htmlspecialchars(date('Y-m-d', strtotime($schedule['datetime']))) . "</li>
                    <li><strong>Time:</strong> " . date('g:i A', strtotime($schedule['datetime'])) . "</li>
                    <li><strong>Responsible Symptoms:</strong> " . htmlspecialchars($schedule['role']) . "</li>
                    <li><strong>Status:</strong> " . htmlspecialchars($schedule['scheduledstatus']) . "</li>
                </ul>
                <p>Please make sure to attend the scheduled appointment.</p>
                <p>Thank you for your service.</p>
            </body>
            </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: pyho9457@gmail.com" . "\r\n";

        // Attempt to send the email
        if (!mail($vetEmail, $subject, $message, $headers)) {
            error_log("Failed to send email to $vetEmail for schedule ID: " . $schedule['appid']);
        } else {
            error_log("Email sent successfully to $vetEmail for schedule ID: " . $schedule['appid']);
        }
    }
}
?>
