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
        $msg = validateData($name, $birthday);

        if (! empty($msg))
        {
            if (! empty($msg["empty"]))
            {
                $error_message = $msg["empty"];
            }
            else if (! empty($msg))
            {
                $validation = $msg;
            }
        }
        else
        {
            $success_message = saveData($name, $birthday);
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
        if (! isset($_SESSION['info']))
            $_SESSION['info'] = [];

        array_push(
            $_SESSION['info'],
            [
                'name' => $name,
                'birthday' => $birthday
            ]
        );

        return "Successfully saved data!
            <br>
            Submit new data or export to XML.";
}

function validateData($name, $birthday)
{
    if (empty($name) || empty($birthday))
    {
        return [
            "empty" => "Please fill in both Name & Birthday",
            "postName" => $name,
            "postBirthday" => $birthday
        ];
    }
    else
    {
        $name_match = preg_match("/[a-z]{2}/", $name);
        $birthday_match = preg_match("/[0-9]{2}-[0-9]{2}-[0-9]{4}/", $birthday);

        $errors = [];

        if ($name_match == 0)
        {
            $errors["name"] = "Should be more than 1 character.";
            $errors["postName"] = $name;
            $errors["postBirthday"] = $birthday;
        }
        if ($birthday_match == 0)
        {
            $errors["birthday"] = "Format should be MM-DD-YYYY.";
            $errors["postName"] = $name;
            $errors["postBirthday"] = $birthday;
        }

        return $errors;
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
                    <input type="text" name="name" id="name" value="<?php echo isset($msg["postName"]) ? $msg["postName"] : ""; ?>" >
                    </div>
                </div>
                <div class="row validation">
                    <div class="col"></div>
                    <div class="col">
                        <?php echo (isset($validation["name"])) ? $validation["name"] : ''; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="#">Birthday:</label>
                        <span>mm-dd-yyyy</span>
                    </div>
                    <div class="col">
                    <input type="text" name="birthday" id="birthday" value="<?php echo isset($msg["postBirthday"]) ? $msg["postBirthday"] : ""; ?>" >
                    </div>
                </div>
                <div class="row validation">
                    <div class="col"></div>
                    <div class="col">
                        <?php echo (isset($validation["birthday"])) ? $validation["birthday"] : ''; ?>
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
