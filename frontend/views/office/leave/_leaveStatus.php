<!--<table class="table table-sm table-bordered col-12 d-none d-sm-block">-->
<div class="d-flex w-100 text-center">
    <div class="text-center col-3 p-3">
        <p>Annual Leave Balance<br>as at <?= date("d/m/Y") ?><br>(Days)</p>
        <h1><?= $leaveStatus->annual_balanceCurrent ?></h1>
    </div>
    <div class="text-center col-3 p-3">
        <p>Annual Leave Balance<br>as at 31/12/<?= date("Y") ?><br>(Days)</p>
        <h1><?= $leaveStatus->annual_balanceYearEnd ?></h1>
    </div>
    <div class="text-center col-3 p-3">
        <p>Sick Leave Balance<br>as at 31/12/<?= date("Y") ?><br>(Days)</p>
        <h1><?= $leaveStatus->sick_balanceYearEnd ?></h1>
    </div>
    <div class="text-center col-3 p-3">
        <p>Pending Leave Request<br/>(Days)</p><br/>
        <h1><?= $leaveStatus->totalPending ?></h1>
    </div>
</div>