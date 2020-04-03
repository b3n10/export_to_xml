<?php
session_start();

if ($_POST)
{
    $name = $_POST['name'];
    $birthday = $_POST['birthday'];

    if (isset($_POST['export']))
    {
        $array_info = $_SESSION['info'];

        if (empty($array_info))
        {
            $error_message = "Submit initial data before exporting.";
        }
        else
        {
            $msg = exportToXML($array_info);

            if ($msg)
            {
                $success_message = $msg;
            }
            else
            {
                $error_message = "Please give write permission to www-data on " . __DIR__;
            }
        }
    }
    else if (isset($_POST['submit']))
    {
        $msg = saveData($name, $birthday);
        if ($msg)
        {
            $error_message = $msg;
        }
    }
}

function exportToXML($array)
{
    $xml = new DOMDocument("1.0", "UTF-8");

    $community = $xml->createElement("community");

    foreach ($array as $arr)
    {
        $member = $xml->createElement("member");
        foreach ($arr as $key => $value)
        {
            $child = $xml->createElement($key, $value);
            $member->appendChild($child);
        }
        $community->appendChild($member);
    }

    $xml->appendChild($community);
    $xml->save("members.xml");

    if (! is_writable("./"))
        return;

    // print $xml->saveXML();
    // die();

    return "Successfully saved to <a href='members.xml'>members.xml</a> ";
}

function saveData($name, $birthday)
{
    if (!empty($name) && !empty($birthday))
    {
        if (! isset($_SESSION['info']))
            $_SESSION['info'] = [];

        array_push(
            $_SESSION['info'],
            [
                'name' => $name,
                'birthday' => $birthday
            ]
        );

        $success_message =
            "Successfully saved data!
            <br>
            Submit new data or export to XML.";
    }
    else
    {
        return "Please fill in both Name & Birthday";
    }
}


// session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>XML Test</title>
</head>
<body>
    <div class="main-container">
        <?php if (isset($error_message)): ?>
            <div class="message error-message">
                <?php echo $error_message; ?>
            </div>
        <?php elseif (isset($success_message)): ?>
            <div class="message success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <h2>Sign-Up</h2>
        <p>Please complete details below</p>
        <hr>
        <form action="" method="POST">
            <div class="form">
                <div class="row">
                    <div class="col">
                        <label for="#">Name:</label>
                        <span>First name only</span>
                    </div>
                    <div class="col">
                        <input type="text" name="name" id="name">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="#">Birthday:</label>
                        <span>mm-dd-yyyy</span>
                    </div>
                    <div class="col">
                        <input type="text" name="birthday" id="birthday">
                    </div>
                </div>
                <div class="row">
                    <div class="col"></div>
                    <div class="col">
                        <button name="submit">Submit</button>
                        <button name="export">Export to XML</button>
                    </div>
                </div>
            </div>
        </form>
        <?php if (isset($_SESSION['info'])): ?>
            <table>
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Birthday</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION['info'] as $array): ?>
                        <tr>
                            <?php foreach($array as $key => $value): ?>
                                <td><?php echo $value; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
