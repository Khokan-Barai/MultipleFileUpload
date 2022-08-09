<?
$db = mysqli_connect("localhost","root","","test");
// echo ($db)? "Yes" : "No";
?>
<style>
    a{
        text-decoration: none;
    }
    tr:nth-child(even) {
        background-color: lightgray;
    }
</style>

<?
    if(isset($_POST['save'])){ //insert first time
        $time = strtotime(date("Y-m-d h:i:s"));

        $originalName_init = $_FILES["file"]["name"];

        $originalName = str_replace("&", "_and_","$originalName_init");//remove & into the file name
        // echo $nameLenght = strlen($originalName); //we can stop to user for log length of name
        if($originalName){
            $newName = $time."_".$originalName;
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $newName);

            $sql = "INSERT INTO `multiple_file`(`pdf`) VALUES ('$newName')";
            $sqlQ = mysqli_query($db, $sql);

            if($sqlQ){
                echo "Insert Successfully.";
                header( "refresh:2;url=http://localhost/multipleFileUpload/index.php" );
            }

        }else{
            echo "Select File First!";
        }
    }
    if(isset($_POST['update']) && $_GET['id']){//insert again into existing id
        $time = strtotime(date("Y-m-d h:i:s"));

        $originalName_init = $_FILES["file"]["name"];
        $originalName = str_replace("&", "_and_","$originalName_init");//remove & into the file name

        if($originalName){
            $newName = $time."_".$originalName;
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $newName);

            $ree = mysqli_fetch_array(mysqli_query($db, "SELECT pdf FROM multiple_file WHERE `id`='$_GET[id]'"));

            if($ree['pdf']){
                $newName =  $ree['pdf'].";".$newName;
            }else{
                $newName =  $newName;
            }

            $sql = "UPDATE `multiple_file` SET `pdf`='$newName' WHERE `id`='$_GET[id]'";
            // echo  $sql;
            $sqlQ = mysqli_query($db, $sql);

            if($sqlQ){
                echo "Update Successfully.";
                header( "refresh:2;url=http://localhost/multipleFileUpload/index.php" );
            }

        }else{
            echo "Select File First!";
        }
    }

    if(isset($_GET['delete']) && $_GET['id']){//delete full row.
        $ree = mysqli_fetch_array(mysqli_query($db, "SELECT pdf FROM multiple_file WHERE `id`='$_GET[id]'"));

        $files = explode(';',$ree['pdf']);
        $totalFiles = count($files);
                        
        for($i=0; $i<$totalFiles; $i++){
            unlink("upload/".$files[$i]);
        }
        $sql = "DELETE FROM `multiple_file` WHERE `id`='$_GET[id]' ";
        $sqlQ = mysqli_query($db,$sql);
        if($sqlQ){
            echo "Delete Successfully.";
            header( "refresh:2;url=http://localhost/multipleFileUpload/index.php" );
        }
    }

    if(isset($_GET['fileDelete']) && $_GET['id'] && $_GET['fileName']){//delete pdf file only
      
        $ree = mysqli_fetch_array(mysqli_query($db, "SELECT pdf FROM multiple_file WHERE `id`='$_GET[id]'"));

        $stringAfter = str_replace("$_GET[fileName]", "","$ree[pdf]");
        $result = preg_replace('/[;]+/', ':', $stringAfter);
        $search = trim($result, ":");
        $final = str_replace(":", ";",$search);

        // echo "Initial: ".$ree['pdf']."<br>"; echo  $stringAfter."<br>";echo $result."<br>"; echo $search."<br>";echo "Final: ".$final."<br>";

        unlink("upload/".$_GET['fileName']);

        $sql = "UPDATE `multiple_file` SET `pdf`='$final' WHERE `id`='$_GET[id]'";
        // echo  $sql;
        $sqlQ = mysqli_query($db, $sql);

        if($sqlQ){
            echo "Delete Successfully.";
            header( "refresh:2;url=http://localhost/multipleFileUpload/index.php" );
        }
        //all right, only one things that is '&', when user use it as a file name then we get error.
    }
?>
<table align="center" border="2" style=" border-collapse: collapse; border-color: gray; width:900px; background-color:lightgray">
    <tr>
        <td>
            <? if(!isset($_GET['edit'])){ ?>
            <form action="" method="POST" style="text-align:center; margin-top:20px;" enctype="multipart/form-data">
                <input type="file" name="file" id="">
                <input type="submit" name="save" value="Save">
            </form>
            <? }else{ ?>
                <form action="" method="POST" style="text-align:center; margin-top:20px;" enctype="multipart/form-data">
                    <input type="file" name="file" id="">
                    <input type="submit" name="update" value="Update">
                </form>
            <? } ?>
        </td>
    </tr>
</table>

<br><br>

<table align="center" border="1" cellpadding="5" style=" border-collapse: collapse; border-color: blue; width:900px;">

    <tr style="background-color:skyblue">
        <th width="60px">ID</th>
        <th>File</th>
        <th>Action</th>
    </tr>
    <?
        $sql = "SELECT * FROM multiple_file";
        $sqlQ = mysqli_query($db, $sql);
        while($ree = mysqli_fetch_array($sqlQ)){ ?>
            <tr>
                <td align="center"><? echo $ree['id']; ?></td>

                <td>
                    <?
                    if($ree['pdf']){

                        $files = explode(';',$ree['pdf']);
                        $totalFiles = count($files);
                        $sl=0;
                        for($i=0; $i<$totalFiles; $i++){ $sl++;
                            echo $sl.": <a target='_blank' href='./upload/$files[$i]'>".$files[$i]."</a>"; 

                            echo "&nbsp;&nbsp;&nbsp;&nbsp;<a style='color:red;' href='./index.php?id=$ree[id]&fileDelete=1&fileName=$files[$i]'>[Delete]</a>";
                    
                            echo "<br>";
                        }
                    }else{
                        echo "PDF NOT FOUND!";
                    }

                    ?>
                </td>

                <td align="center">
                    <a href="./index.php?id=<? echo $ree['id']; ?>&edit=1">[Edit]</a>
                    <a style='color:red;' href="./index.php?id=<? echo $ree['id']; ?>&delete=1">[Delete]</a>
                </td>
            </tr>
    <?  }
    ?>

</table>