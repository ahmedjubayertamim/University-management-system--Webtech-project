<?php
include "../PHP/config.php";

if (!isset($_GET['id'])) {
    die("Invalid request!");
}
$id = intval($_GET['id']);

$sql = "SELECT * FROM offered_course WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Course not found!");
}
$course = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $course_title = $_POST['course_title'];
    $student_capacity = $_POST['student_capacity'];
    $class_time = $_POST['class_time'];
    $class_date = $_POST['class_date'];
    $duration = $_POST['duration'];
    $course_fee = $_POST['course_fee'];

    $update = "UPDATE offered_course SET 
        department='$department',
        course_title='$course_title',
        student_capacity='$student_capacity',
        class_time='$class_time',
        class_date='$class_date',
        duration='$duration',
        course_fee='$course_fee'
        WHERE id=$id";

    if ($conn->query($update)) {
        header("Location: ../view/OfferCourse.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Offered Course</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/editOfferCourseStyle.css">

</head>
<body>
  <div class="form-container">
    <h2>Edit Offered Course</h2>
    <form method="POST">
      <label>Department:</label>
      <input type="text" name="department" value="<?php echo $course['department']; ?>" required>

      <label>Course Title:</label>
      <input type="text" name="course_title" value="<?php echo $course['course_title']; ?>" required>

      <label>Student Capacity:</label>
      <input type="number" name="student_capacity" value="<?php echo $course['student_capacity']; ?>" required>

      <label>Class Time:</label>
      <input type="text" name="class_time" value="<?php echo $course['class_time']; ?>" required>

      <label>Class Date:</label>
      <input type="text" name="class_date" value="<?php echo $course['class_date']; ?>" required>

      <label>Duration (hrs):</label>
      <input type="number" name="duration" value="<?php echo $course['duration']; ?>" required>

      <label>Course Fee (BDT):</label>
      <input type="number" name="course_fee" value="<?php echo $course['course_fee']; ?>" required>

      <button type="submit">Update Course</button>
    </form>
  </div>
</body>
</html>
