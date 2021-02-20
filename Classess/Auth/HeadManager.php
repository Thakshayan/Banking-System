<?php
namespace Classess\Auth;

use Includes\DB\Connection;

class HeadManager extends Manager
{
    
    //constructor
    public function __construct($id, $email, $nic, $fname, $mobileNo, $designation, $branchCode, $DOB, $currentAddress, $dp, $joinedDate)
    {
        parent::__construct($id, $email, $nic, $fname, $mobileNo, $designation, $branchCode, $DOB, $currentAddress, $dp, $joinedDate);
    }

    public function getStaff($ID){
        $sql = "SELECT * FROM employee WHERE ID =?;";
        $stmt = (new Connection)->connect()->prepare($sql);
        
        $stmt->execute([$ID]);

        $result = $stmt->fetch();
        if($result){
            if(strtolower($result['designation']) == 'manager' ){

                $employee = new Employee($result['ID'],$result['email'],$result['NIC'],$result['name'],$result['mobileNo'],$result['designation'],$result['branchCode'],$result['DOB'],$result['Address'],$result['dp'],$result['JoinedDate']);
                $employee->setLeftDate($result['leftDate']);
                return $employee;
            }else{

                return NULL;  
            }   
        }
        return NULL;
    }



    

    public function getBranchCode(){
        
        $sql = "SELECT branchCode FROM branch";
        $stmt = (new Connection)->connect()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $result){
            $optQuery = $optQuery."<option value=".$result['branchCode'].">".$result['branchCode']."</option>";
        }

        return $optQuery;

    }



    public function getAllStaff(){

        $sql = "SELECT `ID`,`name`,`email`,`branchCode`,`mobileNo`,`dp`,`JoinedDate`,`leftDate`,`designation` FROM `employee` WHERE designation = ?";
        $stmt = (new Connection)->connect()->prepare($sql);
        $stmt->execute(['manager']);
        $results = $stmt->fetchAll();
        
        $content = "";
        foreach($results as $result){

            $start = date_create(explode(' ',$result['JoinedDate'])[0]);

            $status = '<a href="#" style="background:#800202;"><i class="fa fa-times" aria-hidden="true"></i></a>';
            if($result['leftDate']==NULL){
                $status = '<a href="#" style="background:green;"><i class="fa fa-check" aria-hidden="true"></i></a>';
                $end = date_create(date('y-m-d',time()));

            }else{
                $end =  date_create(explode(' ',$result['leftDate'])[0]);

            }
            $service = date_diff($start,$end);
            
            $dp = $result['dp'];
            if ( $dp==NULL ){
                $dp = '../../img/contact/1.jpg';
            }


            $content = $content.' <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 search">
                                        <div class="hpanel hblue contact-panel contact-panel-cs mg-t-30">
                                            <div class="panel-body custom-panel-jw">
                                                <div class="social-media-in">
                                                    ID : '.$result['ID'].'<br>
                                                    Status: '.$status.'    
                                                </div>
                                                <img alt="logo" class="img-circle m-b" src="'.$dp.'" style="width:76px; height:76px;">
                                                <h3><a class="managerName" href="">'.$result['name'].'</a></h3>
                                                <p class="all-pro-ad">
                                                    '.$result["designation"].'
                                                </p>
                                                <p>
                                                    Phone No: '.$result['mobileNo'].'<br>
                                                    email: <u><i>'.$result['email'].'</u></i><br>  
                                                    Branch Code: '.$result['branchCode'].'<br>
                                                    Service:  '.$service->format("%R%y years %m mon %a days ").'
                                                    
                                                </p>
                                            </div>
                                    <div class="panel-footer contact-footer" style="margin:auto;  text-align:center;">
                                                <form method="POST" action="edit-professor.php">
                                                    <input type="hidden" value="'.$result['ID'].'"  name="id">
                                                    <button type="submit" class="contact-stat" style="width:100%; cursor:pointer; background-color:#006DF0; border:none;"><span> Read More </span> <strong style="font-size: 15px;"> >>> </strong></button>
                                                </form>
                                    </div>
                                </div>
                            </div>';

        }
        return $content;
                   
    }

    
    
    public function uploadDP($id,$dp){

        $sql = 'SELECT ID,designation FROM employee WHERE ID=?;';
        $stmt = (new Connection)->connect()->prepare($sql);
        if($stmt->execute([$id])){
            $result = $stmt->fetch();
            if($result){
                if(($result['designation']=='manager') | ($result['designation']=='staff') | $id==$this->getID()){
                    $sql = "UPDATE `employee` SET dp=? WHERE `ID`=?";
                    $stmt = (new Connection)->connect()->prepare($sql);
                    if($stmt->execute([$dp,$id])){
                        return SUCCESSFULUPDATE;
                    }
                    return FAILEDINSERT; 
                }
               return PERMISSONDENIED;
            }
            return FAILEDINSERT; 
        } 

        return FAILEDINSERT; 
          
    } 
    
    



}

?>