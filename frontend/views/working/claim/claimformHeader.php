
<div>
    <div>
        <img src="/images/logo_npl.png" style="height:50px;float: left;"/>
        <span style='float:right;bottom: 0px; font-family: calibri;font-style: normal; font-size: 11pt;font-weight: normal' ><br/><?= $model->claimType->claim_name ?><br/>Claim ID: <?= $model->claims_id ?></span>
        <!--<h5 style="width:30%;text-align: right;bottom:0;position: absolute; right:0px" ></h5>-->
    </div>

    <hr style="color:black;border:solid 1px black;margin:5px 0px 5px 0px" />
    <div style="position:relative;margin:5px 0px 5px 0px">

        <table width='100%' style='font-size: 9pt;font-family: calibri;'>
            <tr>
                <td style='width:20%'>
                    Claimant's Name: 
                </td>
                <td style='border-bottom: 1px solid black;text-align: center;width:40%;font-size: 10pt'>
                    <?= $model->claimant->fullname ?>
                </td>
                <td style='text-align:right'>
                    Position: 
                </td>
                <td style='border-bottom: 1px solid black;width:20%'></td>
            </tr>
            <tr>
                <td colspan='3' style='text-align: right'>
                    Page: 
                </td>
                <td style='border-bottom: 1px solid black;text-align:center'>
                    {PAGENO} of {nbpg}
                </td>
            </tr>
        </table>
    </div>


</div>


