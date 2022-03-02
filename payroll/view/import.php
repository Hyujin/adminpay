<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style src='../css/main.css'></style>
        <script src='../js/main.js'></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
    <title>Import</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <img src="../altria.png" alt="altria_logo" height="45vw" class="ms-3 me-5">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <a class="nav-link" aria-current="page" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="import.php">Import</a>
                </li>
            </ul>
            <div class="dropdown me-5">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../res/meow.png" alt="meow" width="35" height="35" class="rounded-circle">
                    <span class="d-none d-sm-inline mx-1">Rochelle</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-light text-small shadow">
                    <li><a class="dropdown-item" href="#">Update Username</a></li>
                    <li><a class="dropdown-item" href="#">Change Password</a></li>
                    <li><a class="dropdown-item" href="#">Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>
    </nav>
    <?php
        include("../controllers/db_conn.php");
        // Get status message
        if(!empty($_GET['status'])){
            switch($_GET['status']){
                case 'empty':
                    $statusType = 'alert-danger';
                    $statusMsg = 'Please select atleast one filter parameter in search.';
                    break;

                default:
                    $statusType = '';
                    $statusMsg = '';

                }
            }

            if (isset($_GET['pageno'])) {
                $pageno = $_GET['pageno'];
            } else {
                $pageno = 1;
            }

            $no_of_records_per_page = 15;
            $offset = ($pageno-1) * $no_of_records_per_page;
            
            $total_pages_sql = "SELECT COUNT(*) FROM employees";
            $result = mysqli_query($db,$total_pages_sql);
            $total_rows = mysqli_fetch_array($result)[0];
            $total_pages = ceil($total_rows / $no_of_records_per_page);   
    ?>
    <div class="container-fluid ms-3">
    <div class="row">
            <div class="col-8 py-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Regular Employees</li>
                </ol>
                </nav>
            </div>
            <div class="col-4 py-2 mb-3">
                <input type="text" class="text" id="search" placeholder=" Search...">
                <button type="submit" class="btn btn-sm btn-primary"> <i class="fs-5 bi-search"></i></button>
            
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Import Payroll CSV">
                        <a href="javascript:void(0);" class="btn btn-success btn-sm import-toggle" onclick="formToggle('importFrm');" ><i class="fs-5 bi-cloud-arrow-up"></i></a>
                    </span>  
                </div>
       </div>

       <div class="row">
        <div class="col-6"></div>
        <div class="col-6 import-toggle" id="importFrm" style="display: none;">
            <div class="input-group input-group-sm position-relative">
                <form action="../controllers/emp_Import.php" method="post" enctype="multipart/form-data">
                    <div class="form-control" style="border: 0;">
                        <input class="form-control form-control-sm" id="formFileSm"  type="file" name="file" />
                        <input type="submit" class="btn btn-primary btn-sm position-absolute top-50 end-0 translate-middle-y" name="importSubmit" value="IMPORT">
                    </div>
                </form>
            </div>  
        </div>
       </div>
    </div>
    <div class="container-fluid">
        <div class="table table-sm">
        <table class="table table-sm table-responsive table-hover table-light mt-3">
                <thead class="table-primary">
                    <th class="col">
                        <input type="checkbox" class="radio ms-3" id="allMarked">
                    </th>
                    <th class="col text-start"><small>Name</small></th>
                    <th class="col text-center"><small>Role</small></th>
                    <th class="col text-center"><small>Manhour</small></th>
                    <th class="col text-center"><small>Total Deductions</small></th>
                    <th class="col text-center"><small>OT Pay</small></th>
                    <th class="col text-center"><small>Gross Pay</small></th>
                    <th class="col text-center"><small>Net Pay</small></th>
                    <th class="col text-center"><small>Date</small></th>
                    <th class="col text-center"><small>Status</small></th>
                    <th class="col-2  text-center"><small>Tool</small></th> 
                </thead>
                <tbody>
                <?php        
                $sql = "SELECT employees.fullname, employees.role, reg_manhour.total_worked_hrs, deductions.total_deductions, reg_pay.ot_pay, reg_pay.gross_pay, reg_pay.net_pay, reg_pay.timestamps, reg_pay.visibility
                        FROM reg_pay
                        INNER JOIN employees ON reg_pay.emp_id = employees.id
                        INNER JOIN reg_manhour ON reg_pay.emp_id = reg_manhour.emp_id
                        INNER JOIN deductions ON reg_pay.emp_id = deductions.emp_id
                        GROUP BY employees.id
                        ORDER BY fullname ASC LIMIT $offset, $no_of_records_per_page ";

                        $result = $db->query($sql);
                        if ($result->num_rows > 0) {
                            // output data of each row
                            while($row = $result->fetch_assoc()) {
                                 echo "<tr class='text-center align-middle'>";
                                 echo "<form action='edit.php' method='post'>";
                                 echo "<td scope='col'> <input class='ms-3' type='radio'> </td>";
                                 echo "<td scope='col'> <small> <input style='border: 0;' type='text' size='16' name='id' class='hidden text-start' disabled value=\"" .$row['fullname']. "\" > </small></td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='8' name='fullname' class='hidden text-center' disabled value=\"" . $row["role"] . "\" > </td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' id='order_id' size='14' name='order_id' disabled class='hidden text-center' value=\"" . $row["total_worked_hrs"] . "\" > </td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='10' name='cx_phone' class='hidden text-center' disabled value=".$row['total_deductions']." > </td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='5' name='disposition' class='hidden text-center' disabled value=\"" . $row["ot_pay"] . "\" > </td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='12' name='amount' class='hidden text-center' disabled  value=\"" .$row['gross_pay']. "\"></td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='12' name='amount' class='hidden text-center' disabled value=\"" .$row['net_pay']. "\"></td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='12' name='amount' class='hidden text-center' disabled value=\"" .$row['timestamps']. "\"></td>";
                                 echo "<td scope='col'> <input style='border: 0;' type='text' size='12' name='amount' class='hidden text-center' disabled value=\"" .$row['visibility']. "\"></td>";
                                 echo "<td scope='col'> 
                                 <button class='pr-1 btn btn-sm btn-primary'><i class='fs bi-pencil-square'></i></button>
                                 <button class='pr-1 btn btn-sm btn-success'><i class='fs bi-printer'></i></button>
                                 </td>";
                                 echo "</form>";
                                 echo "</tr>";
                                }
                          } 
                          else {
                            echo "<tr><td colspan='12' class='text-center'>No record(s) found...</td></tr>";
                          }

                          $db->close();

                          ?>
                </tbody>
            </table>
   
        </div>
        <p class="text-center">Page <?php echo"$pageno" ?> of <?php echo"$total_pages" ?></p>
        <nav class="container position-relative mb-5">
                <ul class="pagination position-absolute top-50 start-50 translate-middle mt-4">
                    <li><a href="?pageno=1" class="btn btn-sm btn-dark ms-3 me-3">First</a></li>
                    <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>" class="btn btn-sm btn-dark ms-3 me-3">
                        <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>" class="btn btn-sm btn-dark ms-3 me-3">Prev</a>
                    </li>
                    <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>" class="btn btn-sm btn-dark ms-3 me-3">Next </a>
                    </li>
                    <li><a href="?pageno=<?php echo $total_pages; ?>" class="btn btn-sm btn-dark ms-3 me-3">Last</a></li>
                </ul>
        </nav>

</body>
</html>