<?php
include 'config.php'; // Database Connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>State & City Dropdown</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Select State and City</h2>

    <label for="state">State:</label>
    <select id="state" name="state">
        <option value="">Select State</option>
        <?php
        $query = $dbh->query("SELECT * FROM states ORDER BY name ASC");
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label for="city">City:</label>
    <select id="city" name="city">
        <option value="">Select City</option>
    </select>

    <script>
        $(document).ready(function() {
            $("#state").change(function() {
                var state_id = $(this).val();
                
                $.ajax({
                    url: "get_cities.php",
                    method: "POST",
                    data: { state_id: state_id },
                    success: function(data) {
                        $("#city").html(data);
                    }
                });
            });
        });
    </script>

</body>
</html>
