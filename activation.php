<?
$msg = '';
include "gincludes.php";
$pr = 'login';
$base_url = base_url();
$STB_OBJ = stbx();
$COMP_OBJ = compx();
$Is_User_Login = $STB_OBJ->is_user_login();
if(!$Is_User_Login) { header("location:".$base_url."login/");}

$DB_OBJ = new DB();
// GET URL PARA
$PAGE_REQ = NULL;
$URL = $_SERVER['REQUEST_URI'];
$URL = trim($URL,"/");
$act = $para1 = $para2 = $USER_INFO =  $MSG = NULL;
if(isset($_GET['act'])) $act =  trim($_GET['act'],"/");
$userID =  $_SESSION['userID'];
if($URL  == 'activation' )
  {

      if($_POST)
        {
        //  print_r($_POST);
          $TXID = $_POST['TXID'];
          $PACK = $_POST['matrix_x'];
          $REQ_AMOUNT = $COMP_OBJ->get_package_btc($PACK);

          $curl = curl_init();
          curl_setopt_array($curl, [
              CURLOPT_RETURNTRANSFER => 1,
              CURLOPT_URL => $base_url . 'ap/?act=gettr-2-'.$TXID."-".rand(6666,454545454)
          ]);
          $RESP = curl_exec($curl);
          curl_close($curl);

          $TXX = $RESP;
          if($RESP && !strpos($TXX, 'Invalid') !== false)
          {
            $TR_DATA = json_decode($RESP,1);
            $AMOUNT_REC = $TR_DATA['amount'];

            if($REQ_AMOUNT <= $AMOUNT_REC) // valid
            {

              $DB_CHECK[0] = $DB_OBJ->query("SELECT * FROM sg_used_transaction_ids WHERE txnID = '$TXID'");
            //  print_r($DB_CHECK);
              //exit();
              if(!$DB_CHECK[0])
                {

                  $ins = NULL;
									$ins['txnID'] = $TR_DATA['txid'];
									$ins['amount'] = $TR_DATA['amount'];
									$ins['userID'] = $userID;
									$ins['insTime'] = time();
									$DB_OBJ->gInsert('sg_used_transaction_ids',$ins);

                  //print_r($ins);
                  $COMP_OBJ->activate_binary($userID,$TR_DATA['amount'],$TR_DATA['txid']);
                  $_SESSION['userPackage'] = 1;
  		            header("location:".$base_url."myaccount/");

                }
              else
               {

                    $msg = "<div class='alert alert-danger'>Transaction is in use. </div>";
              }



            }
            else // add to used transaction id
            {
                $msg = "<div class='alert alert-danger'>Invalid Transaction amount! </div>";
            }

          }
          else {
            $msg = "<div class='alert alert-danger'>Transaction ID not found! </div>";
          }




        }


      $PAGE_REQ = "activation";
      $SP_INFO = [];
      $wh = NULL;
      $wh['userID'] = $userID;
      $USER_DATA = $DB_OBJ->gSelect('sg_users',$wh);

      $wh = NULL;
      $wh['userID'] = $USER_DATA['userID'];
      $REF_DATA = $DB_OBJ->gSelect('sg_users',$wh);


  }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
          <base url = "<?=$base_url?>">
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="description" content="">
          <meta name="author" content="">
          <link rel="shortcut icon" href="<?=$base_url?>cdn/backoffice/images/favicon.png">
          <title>Dashboard</title>

          <!-- Bootstrap -->
          <link href="<?=$base_url?>cdn/backoffice/css/bootstrap.min.css" rel="stylesheet">
          <link href="<?=$base_url?>cdn/backoffice/css/style.css" rel="stylesheet">
          <link href="<?=$base_url?>cdn/backoffice/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
          <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
          <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">
          <link href="<?=$base_url?>cdn/backoffice/css/SidebarNav2.min.css" rel="stylesheet">
          <!--[if IE]>
          <link rel="stylesheet" type="text/css" href="css/style_ie.css"/>
          <![endif]-->
          <script src="<?=$base_url?>cdn/backoffice/js/css3-mediaqueries.js"></script>

          <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
          <!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->
          <script src="<?=$base_url?>cdn/backoffice/js/ie-emulation-modes-warning.js"></script>

          <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
          <!--[if lt IE 9]>
          <script src="js/html5shiv.min.js"></script>
          <script src="js/respond.min.js"></script>
          <![endif]-->
  </head>
  <body>
    <aside class="sidebar-left">
	<div class="clsBg_blc clsPad_In_10"><img src="<?=$base_url?>cdn/backoffice/images/logo.png" class="img-responsive center-block" width="140"/></div>



      <? include getcwd()."/ma/leftpanel.php"; ?>



    </aside>

	<div class="content">



    <? include getcwd()."/ma/header.php"; ?>





      <? if ($PAGE_REQ == 'activation') { ?>

<div class="container-fluid">
        <div class="row clsPad_Top20" >
          <div class="col-md-12">
            <div class="clsCont_box clsMarg_Bot10">
              <div class="clsTitle clsBg_blc clsPad_In_10 clsTxt_wht clsTxt_up"> Activate STB Account </div>
              <div class="clsContent clsBg_wht clsPadding20 clsBx_shdw">
                <div class="table-responsive table-full-width">

                  <?=$msg?>


  <form action="" method="post" id="act_form" name="act_form">





                  Sponsor Details
                  <hr>
                  <table width="100%">
                  <tbody>
                  <tr>
                  <td width="50%">Username</td>
                  <td> <?=$REF_DATA['username']?> </td>
                  </tr>
                  <tr>
                  <td width="50%">Sponsor Name</td>
                  <td> <?=$REF_DATA['firstname']?> <?=$REF_DATA['lastname']?> </td>
                  </tr>
                  <tr>
                  <td width="50%">Sponsor Email </td>
                  <td> <?=$REF_DATA['emailID']?> </td>
                  </tr>
                  <tr>
                  <td width="50%">Sponsor Join Date</td>
                  <td>08/03/2019</td>
                  </tr>
                  <tr>
                  <td colspan="2">&nbsp;</td>
                  </tr>
                  </tbody>
                  </table>

                  <input type="hidden" name="st_amount" id="st_amount" value="5">

                  Choose Your Plan <br>
                   <hr>
                  <input checked="checked" type="radio" onClick="live_v('0.0100',1);" name="matrix_x" value="1"> Plan 1 - Ƀ 0.0100 <br>
                  <input type="radio" onClick="live_v('0.0250',2);" name="matrix_x" value="2"> Plan 2 - Ƀ 0.0250 <br>
                  <input type="radio" onClick="live_v('0.1000',3);" name="matrix_x" value="3"> Plan 3 - Ƀ 0.1000 <br>
                  <input type="radio" onClick="live_v('0.50',4);" name="matrix_x" value="4" checked="checked"> Plan 4 - Ƀ 0.50 <br>
                  <input type="radio" onClick="live_v('1.00',5);" name="matrix_x" value="5"> Plan 5 - Ƀ 1.00 <br>
                  <input type="radio" onClick="live_v('2.00',6);" name="matrix_x" value="6"> Plan 6 - Ƀ 2.00 <br>
                  <input type="radio" onClick="live_v('3.00',7);" name="matrix_x" value="7"> Plan 7 - Ƀ 3.00 <br>
                  <input type="radio" onClick="live_v('5.00',8);" name="matrix_x" value="8"> Plan 8 - Ƀ 5.00 <br>
                  <br><br><br>

                  <h3> Step 1: Send the bitcoin to purchase your plan <label id="jax_l_1"> </label> to the address below. </h3>
                  <hr>
                  <table width="" class="table text-centered">
                  <tr>
                  <td width="48%">
                  <center>
                  <img src="<?=$base_url?>cdn/corp_0.png" id="jpmd100cr_now" width="200">
                  </center>
                  </td>
                  <td> OR </td>
                  <td width="48%">
                  <center>
                  <img src="<?=$base_url?>cdn/corp_1.png" id="jpmd100cr_now1" width="200">
                  </center>
                  </td>
                  </tr>
                  <tr>
                  <td width="100%" colspan=3>
                  <div  >



                    <center>
                  <div class="col-md-8">
                  <input type="text" readonly="" id="btcAddressCopy" class="form-control" value="1EpVp8cJY7HyRZXRzw4g1YUwEKdvMBtd9p" style="font-size:18px; font-weight:bold; height:42px;padding-left:20px;border:2px solid #dcdcdc;border-radius:5px;background:white;margin-top:5px;text-align:center;">
                  </div>
                  <br>

                  <div class="col-md-2">
                  <button type="button" class="btn  dashbtn1" onclick="myFunction()">Copy</button>
                  </div>
                  <script>
                  				function myFunction() {
                  				  var copyText = document.getElementById("btcAddressCopy");
                  				  copyText.select();
                  					 document.execCommand("Copy");
                  						alert("Copied the text: " + copyText.value);
                  						}
                  				</script>
                  </center></div>
                  </td>
                  </tr>
                  </table>
                  <br>

                  <h3> Step 2: Verify Payment </h3>
                  <hr>
                  To verify your payment please enter transaction ID into below box.<br><br>

                    <input type="text"  id="TXID" name="TXID" class="form-control" placeholder="Your Bitcoin Payment Transaction ID" style="font-size:18px; font-weight:bold; height:42px;padding-left:20px;border:2px solid #dcdcdc;border-radius:5px;background:white;margin-top:5px;text-align:center;">

                    <br>
                    <button type="button" class="btn  dashbtn1 btn-success" onclick="fetch_tr()">Fetch Latest Transactions</button>
                      <div id="DD_IDS" style="padding-top:30px;"></div>

<br><br>
                    <h3> Step 3: Activate Account </h3>
                    <hr>

                  <div class="alert alert-danger">
                  <div style="text-align:center; color:#FFFFFF;">
                  <center>
                  <b>
                  WHEN YOUR PAYMENT IS COMPLETE & YOU HAVE ADDED TRANSACTION ID INTO ABOVE BOX CLICK BELOW BUTTON<br><br>
                  <a class="btn btn-lg btn-primary dashbtn1" href="javascript:void(0);" onClick = "submit_form();"> TAKE ME TO THE DASHBOARD! >> </a>
                  </b>
                  </center>


  </form>



<script>
    function fetch_tr()
        {
          $('#DD_IDS').html("Please wait ...");
          $.get( "<?=$base_url?>ap/?act=listtr-2-"+Math.random(), { } )
              .done(function( data ) {


                var arr1 = [];
                var dp = '<table class="table table-hover table-striped" width="100%" style="font-size:11px;" align="center"><tr><td> <b> AMOUNT </b> </td><td> <b> TXID </b> </td><td> <b> ACTION </b> </td></tr>';

                var obj = JSON.parse(data);
                //obj = reverseObject(obj1);

                for (var i = 0; i < obj.length; i++) {

                  tm = obj[i].amount;
                  tx = obj[i].txid;
                  arr1.push([tx,tm]);


                }

                arr1.reverse();

                var arrayLength = arr1.length;
                for (var i = 0; i < arrayLength; i++) {

                      dp += '<tr><td style="height:25px;"> '+arr1[i][1]+' </td><td> '+arr1[i][0]+' </td><td> <a onClick="copyTR(\''+arr1[i][0]+'\')" href="javascript:void(0);" style=" text-decoration: none;" class="badge badge-success" href="">Use This TXID</a></td></tr>';
                }

                if(arr1.length == 0) {

                    dp += '<tr><td style="height:25px;" colspan="3"> No transactions found!</td></tr>';
                }

                //   dp += '<tr><td> '+obj[i].amount+' </td><td> '+obj[i].txid+' </td><td> Use This TXID </td></tr>';
                  dp += '</table>';
                  $('#DD_IDS').html(dp);


              });
        }

    function submit_form()
      {
        if(document.getElementById("TXID").value == '')
        {
            alert("Please enter transaction ID, or click on Fetch Latest Transactions and select your transaction before activation.");
        }
        else
        {
          var sl = document.getElementById("TXID").value;
          s1 = sl.trim();
          if(s1.length == 64)
            document.getElementById("act_form").submit();
          else {
            alert("Invalid Transaction ID");
          }
        }

      }

    function copyTR(tid)
      {
          $('#TXID').val(tid);
      }

    function live_v(){}
</script>

























                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>







     <?  } ?>



























		<div class="clsBg_blc navbar-fixed-bottom">
			<div class="container-fluid">
				<div class="clsPad_In_5">
					<div class="row">
						<div class="col-md-12">
							<p class="clsTxt_wht text-center clsFnt_sm">Copyright © 2019 - All Rights Reserved.</p>
						</div>
					</div>
				</div>
			</div>	<!--container-->
		</div>

	</div>
  <script src="<?=$base_url?>cdn/backoffice/js/jquery-1.11.0.min.js"></script>
  <script src="<?=$base_url?>cdn/backoffice/js/bootstrap.min.js"></script>
  <script src="<?=$base_url?>cdn/backoffice/js/ie10-viewport-bug-workaround.js"></script>
	<script src="<?=$base_url?>cdn/backoffice/js/twitter-bootstrap-hover-dropdown.js"></script>
	<script>
		$(document).ready(function() {
		  $('.js-activated').dropdownHover().dropdown();
		});
	</script>
	<script src="<?=$base_url?>cdn/backoffice/js/navAccordion.min.js"></script>
	<script>
		jQuery(document).ready(function(){

			//Accordion Nav
			jQuery('.mainNav').navAccordion({
				expandButtonText: '<i class="fa fa-plus"></i>',  //Text inside of buttons can be HTML
				collapseButtonText: '<i class="fa fa-minus"></i>'
			},
			function(){
				console.log('Callback')
			});

		});
	</script>
  </body>
</html>
