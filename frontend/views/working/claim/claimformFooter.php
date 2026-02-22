

<br/>
<div>
    <table width='100%' style="font-size: 7pt;">
        <tr>
            <td colspan="2">
                <b style='font-size: 10pt;'>DECLARATION</b>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                I declare that: -
            </td>
        </tr>
        <tr >
            <td style='text-align: center;width:100px;vertical-align: top' >&#9745;</td>
            <td>The amounts of expenses claimed, have been actually and reasonably incurred for the purpose of enabling me to perform approved duties as a empoyee of NPL Engineering Sdn. Bhd.</td>
        </tr>
        <tr>
            <td style='text-align: center;vertical-align: top'>&#9745;</td>
            <td>I have not made any other claim which is not connected to the duties indicated in this form.</td>
        </tr>
        <tr>
            <td style='text-align: center'>&#9745;</td>
            <td>I have attached all necessary receipts in connection with expenses claimed.</td>
        </tr>
    </table>
    <table  width='100%' style="font-size: 7pt;">
        <tr><td colspan='3'>&nbsp;</td></tr>
        <tr><td colspan='3'>&nbsp;</td></tr>

        <tr>
            <td style='width:15%'>Claimant's Signature</td>
            <td style='width:35%;border-bottom: 1px solid black'><?= $model->claim_type=="tra"?"(COMPUTER GENERATED, NO SIGNATURE REQUIRED)":"" ?></td>
            <td style='text-align: right'>
                Date:
            </td>
            <td style='width:20%;border-bottom: 1px solid black;font-size: 10pt;text-align: center'>
                <?= \common\models\myTools\MyFormatter::asDate_Read($model->created_at) ?>
            </td>
        </tr>
    </table>
    <hr style="color:black;border:solid 1px black;margin:5px 0px 0px 0px" />
    <table  width='100%' style="font-size: 7pt;">
        <tr>
            <td colspan="4">
                <b>FINANCE USE ONLY</b>
            </td>
        </tr>
        <tr><td colspan='4'  style='height: 25px'>&nbsp;</td></tr>
        <tr>
            <td style='width:15%'>Checked By </td>
            <td style='width:35%;border-bottom: 1px solid black'></td>
            <td style='text-align: right'>
                Date:
            </td>
            <td style='width:20%;border-bottom: 1px solid black;font-size: 10pt;text-align: center'></td>
        </tr>
        <tr><td colspan='4'  style='height: 25px'>&nbsp;</td></tr>
        <tr>
            <td style='width:15%'>Approved By </td>
            <td style='width:35%;border-bottom: 1px solid black'></td>
            <td style='text-align: right'>
                Date:
            </td>
            <td style='width:20%;border-bottom: 1px solid black;font-size: 10pt;text-align: center'></td>
        </tr>
    </table>
</div>



