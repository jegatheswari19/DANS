<?php
include('../db_con.php');

if (isset($_POST['sem']) && isset($_POST['dept'])) {
    $sem = $_POST['sem'];
    $dept = $_POST['dept'];

    $query = mysqli_prepare($con, "SELECT RegNo, name FROM student WHERE sem = ? AND department = ? AND batch IS NULL");
    mysqli_stmt_bind_param($query, "ss", $sem, $dept);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $id, $name);

    $students = [];
    while (mysqli_stmt_fetch($query)) {
        $students[] = ['id' => $id, 'name' => $name];
    }

    if (count($students) > 0) {
        echo '<table><tr><th>ID</th><th>Name</th><th>Batch</th></tr>';
        foreach ($students as $student) {
            echo '<tr>
                    <td>' . $student['id'] . '</td>
                    <td>' . $student['name'] . '</td>
                    <td>
                        <select name="batch[' . $student['id'] . ']">
                            <option value="1">Batch 1</option>
                            <option value="2">Batch 2</option>
                        </select>
                    </td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo 'No students found for the selected semester and department.';
    }

    mysqli_stmt_close($query);
}
?>
