<?php
if (isset($_POST['structure'])) {
    $source = str_replace("\r", "", $_POST['structure']);

    $arr = explode("\n", $source);

    function parseColumn($val)
    {
        if (in_array($val, ['name', 'title'])) {
            return '`' . $val . '` varchar(255) NOT NULL';
        } else {
            return '`' . $val . '` int(11) NOT NULL';
        }
    }

    function autoIncrement()
    {
        return '`id` INT NOT NULL AUTO_INCREMENT';
    }

    foreach ($arr as $key => $value) {
        if (strpos($value, "\t") !== false) {
            $value = trim($value);
            $output[$parent][$value] = parseColumn($value);
        } else {
            $parent = trim($value);
            $output[$parent] = [];
            $output[$parent]['id'] = autoIncrement();

        }
    }
    $sqlOutput = '';
    foreach ($output as $key => $value) {
        $sqlOutput .= 'DROP TABLE IF EXISTS `' . $key . '`; CREATE TABLE `' . $key . '` (' . "\n" . implode(", \n", $value) . ', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;' . "\n";
    }
    try {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $db = new PDO('mysql:host=localhost;dbname=' . $_POST['dbname'], 'root', '', $pdo_options);
        $query = $sqlOutput;
        $req = $db->prepare($query);
        $req->execute();
    } catch (Exception $e) {
        echo 'db error';
    }
}
?>

<style>
    body {
        tab-size: 3;
    }
</style>
<form action="" method="POST">
    <div><input type="text" name="dbname" value="db-flash-prototype"></div>
    <div><textarea id="structure" name="structure" cols="50" rows="20"></textarea></div>

    <input type="submit" value="Create">
</form>
<script>
    function enableTab(id) {
        var el = document.getElementById(id);
        el.onkeydown = function (e) {
            if (e.keyCode === 9) { // tab was pressed

                // get caret position/selection
                var val = this.value,
                    start = this.selectionStart,
                    end = this.selectionEnd;

                // set textarea value to: text before caret + tab + text after caret
                this.value = val.substring(0, start) + '\t' + val.substring(end);

                // put caret at right position again
                this.selectionStart = this.selectionEnd = start + 1;

                // prevent the focus lose
                return false;

            }
        };
    }

    enableTab('structure');
</script>