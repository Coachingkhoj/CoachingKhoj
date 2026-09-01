<?php
// Apni Database details yahan daalein
$db_host = "localhost";
$db_user = "YOUR_DB_USER";
$db_pass = "YOUR_DB_PASSWORD";
$db_name = "YOUR_DB_NAME";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

$city_filter = isset($_GET["city"]) ? $conn->real_escape_string($_GET["city"]) : "";
$exam_filter = isset($_GET["exam"]) ? $conn->real_escape_string($_GET["exam"]) : "";

if (isset($_GET["export"]) && $_GET["export"] == "csv") {
    $sql_export = "SELECT id, student_name, phone_number, target_exam, preferred_city, current_class, created_at FROM neet_jee_leads WHERE 1=1";
    if (!empty($city_filter)) { $sql_export .= " AND preferred_city = '$city_filter'"; }
    if (!empty($exam_filter)) { $sql_export .= " AND target_exam = '$exam_filter'"; }
    $sql_export .= " ORDER BY created_at DESC";

    $result_export = $conn->query($sql_export);
    $filename = "CoachingKhoj_Leads_" . date("Y-m-d") . ".csv";

    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=" . $filename);

    $output = fopen("php://output", "w");
    fputcsv($output, array("ID", "Student Name", "Phone Number", "Target Exam", "City", "Class", "Date"));

    if ($result_export->num_rows > 0) {
        while ($row = $result_export->fetch_assoc()) {
            $row["phone_number"] = "'" . $row["phone_number"];
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit();
}

$sql = "SELECT * FROM neet_jee_leads WHERE 1=1";
if (!empty($city_filter)) { $sql .= " AND preferred_city = '$city_filter'"; }
if (!empty($exam_filter)) { $sql .= " AND target_exam = '$exam_filter'"; }
$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>CoachingKhoj Admin</title>
  <style>
    body { font-family: sans-serif; padding: 20px; background: #f8fafc; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
    th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
    th { background: #0f172a; color: white; }
  </style>
</head>
<body>
  <h2>CoachingKhoj — Student Leads Panel</h2>
  <form method="GET" style="margin: 15px 0;">
    City: 
    <select name="city">
      <option value="">All</option>
      <option value="Delhi">Delhi</option>
      <option value="Noida">Noida</option>
    </select>
    Exam: 
    <select name="exam">
      <option value="">All</option>
      <option value="NEET">NEET</option>
      <option value="JEE Main">JEE Main</option>
      <option value="JEE Advanced">JEE Advanced</option>
    </select>
    <button type="submit">Filter</button>
    <a href="admin_panel.php?export=csv&city=<?php echo $city_filter; ?>&exam=<?php echo $exam_filter; ?>" style="background: green; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-left: 15px;">📥 Download Excel/CSV</a>
  </form>
  <table>
    <tr><th>ID</th><th>Name</th><th>Phone</th><th>Exam</th><th>City</th><th>Class</th><th>Date</th></tr>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
      <td>#<?php echo $row["id"]; ?></td>
      <td><?php echo $row["student_name"]; ?></td>
      <td><b><?php echo $row["phone_number"]; ?></b></td>
      <td><?php echo $row["target_exam"]; ?></td>
      <td><?php echo $row["preferred_city"]; ?></td>
      <td><?php echo $row["current_class"]; ?></td>
      <td><?php echo $row["created_at"]; ?></td>
    </tr>
    <?php } ?>
  </table>
</body>
</html>