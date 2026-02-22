<style>
    .card {
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
        box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
        border: none;
        margin-bottom: 30px;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }
    .bg-c-blue {
        background: linear-gradient(45deg,#4099ff,#73b4ff);
    }
    .order-card {
        color: #fff;
    }
</style>

<fieldset class="form-group border p-3">
    <legend class="w-auto px-2 m-0">Overall</legend>
    <div class="row">
        <div class="col-xl-6">
            <div class="card bg-c-blue order-card">
                <div class="p-4">
                    <h6 class="m-b-20">Overall Rating</h6>
                    <h1 class="text-center"><?= $vModel->overall_rating ?></h1>
                    <p class="m-b-0">Appraised By<span class="float-right"><?= $vModel->appraise_by ?></span></p>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card bg-c-blue order-card">
                <div class="p-4">
                    <h6 class="m-b-20">Overall Review</h6>
                    <h1 class="text-center"><?= $vModel->overall_review ?></h1>
                    <p class="m-b-0">Reviewed By<span class="float-right"><?= $vModel->review_by_name ?></span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12 m-0 p-0">
        <p>Staff Remark: <span class=""><?= $vModel->staff_remark ?></span></p>
        <p>Fullmark: <span class=""><?= 35 ?></span></p>
    </div>
</fieldset>