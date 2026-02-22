<!--<div class="col-lg-12 col-md-12 col-sm-12"> 
    <div class="banner enhanced-banner"> 
        <i class="fas fa-exclamation-triangle"></i>
        <span>Your contribution this month is under 10% - one of our lowest performers</span>
    </div> 
</div>

<style> 
.enhanced-banner { 
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    border-left: 5px solid #a71e2a;
    color: white;
    padding: 18px 25px;
    text-align: center;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    animation: slideInPulse 3s ease-in-out infinite;
    font-weight: 500;
    font-size: 16px;
    margin-bottom: 20px;
} 

.enhanced-banner i.fa-exclamation-triangle {
    margin-right: 10px;
    font-size: 18px;
    animation: shake 1s ease-in-out infinite;
}

.close-banner {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close-banner:hover {
    color: white;
}

@keyframes slideInPulse { 
    0% { opacity: 0.8; transform: translateY(-5px); } 
    50% { opacity: 1; transform: translateY(0); } 
    100% { opacity: 0.8; transform: translateY(-5px); } 
} 

@keyframes shake {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-5deg); }
    75% { transform: rotate(5deg); }
}

.enhanced-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    transition: all 0.3s ease;
}
</style>-->

<!--<div class="col-lg-12 col-md-12 col-sm-12"> 
    <div class="banner top-performer-banner"> 
        <i class="fas fa-trophy"></i>
        <span>Congratulations! You're one of our top performers this month with outstanding contribution!</span>
    </div> 
</div>

<style> 
.top-performer-banner { 
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    border-left: 5px solid #1e7e34;
    color: white;
    padding: 18px 25px;
    text-align: center;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    animation: celebrateGlow 3s ease-in-out infinite;
    font-weight: 500;
    font-size: 16px;
    margin-bottom: 20px;
} 

.top-performer-banner i.fa-trophy {
    margin-right: 10px;
    font-size: 18px;
    animation: bounce 2s ease-in-out infinite;
    color: #ffd700;
}

@keyframes celebrateGlow { 
    0% { opacity: 0.9; transform: scale(1); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); } 
    50% { opacity: 1; transform: scale(1.02); box-shadow: 0 6px 25px rgba(40, 167, 69, 0.5); } 
    100% { opacity: 0.9; transform: scale(1); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); } 
} 

@keyframes bounce {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    25% { transform: translateY(-5px) rotate(-10deg); }
    50% { transform: translateY(-8px) rotate(0deg); }
    75% { transform: translateY(-3px) rotate(10deg); }
}

.top-performer-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    transition: all 0.3s ease;
}
</style>-->
<?php
use yii\helpers\Html;

// Only show banner if applicable
if (isset($bannerData['showBanner']) && $bannerData['showBanner']) {
    if ($bannerData['bannerType'] === 'congratulations') {
        $bannerClass = 'top-performer-banner';
        $iconClass = 'fas fa-trophy';
    } elseif ($bannerData['bannerType'] === 'warning') {
        $bannerClass = 'warning-banner';
        $iconClass = 'fas fa-exclamation-triangle';
    } else {
        $bannerClass = 'encouragement-banner';
        $iconClass = 'fas fa-chart-line';
    }
?>
<div class="col-lg-12 col-md-12 col-sm-12"> 
    <div class="banner <?= $bannerClass ?>"> 
        <i class="<?= $iconClass ?>"></i>
        <span><?= Html::encode($bannerData['message']) ?></span>
    </div> 
</div>
<style> 
/* Congratulations Banner (Tier Achievement) */
.top-performer-banner { 
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    /*border-left: 5px solid #1e7e34;*/
    color: white;
    padding: 18px 25px;
    text-align: center;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    animation: celebrateGlow 3s ease-in-out infinite;
    font-weight: 500;
    font-size: 16px;
    margin-bottom: 20px;
} 

.top-performer-banner i.fa-trophy {
    margin-right: 10px;
    font-size: 18px;
    animation: bounce 2s ease-in-out infinite;
    color: #ffd700;
}

/* Warning Banner (Alert - Under Tier 1, 1 Week Before Cutoff) */
.warning-banner {
    background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
    border: none;
    /*border-left: 5px solid #c92a2a;*/
    color: white;
    padding: 18px 25px;
    text-align: center;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    animation: urgentPulse 1.5s ease-in-out infinite;
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 20px;
}

.warning-banner i.fa-exclamation-triangle {
    margin-right: 10px;
    font-size: 18px;
    animation: shake 0.8s ease-in-out infinite;
    color: #fff3cd;
}

/* Encouragement Banner (Not Yet Achieved) */
.encouragement-banner {
    background: linear-gradient(135deg, #17a2b8, #138496);
    border: none;
    /*border-left: 5px solid #117a8b;*/
    color: white;
    padding: 18px 25px;
    text-align: center;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
    animation: gentlePulse 3s ease-in-out infinite;
    font-weight: 500;
    font-size: 16px;
    margin-bottom: 20px;
}

.encouragement-banner i.fa-chart-line {
    margin-right: 10px;
    font-size: 18px;
    color: #ffffff;
    animation: slideUp 2s ease-in-out infinite;
}

/* Animations */
@keyframes celebrateGlow { 
    0% { opacity: 0.9; transform: scale(1); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); } 
    50% { opacity: 1; transform: scale(1.02); box-shadow: 0 6px 25px rgba(40, 167, 69, 0.5); } 
    100% { opacity: 0.9; transform: scale(1); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); } 
} 

@keyframes urgentPulse {
    0% { opacity: 0.95; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3); }
    50% { opacity: 1; box-shadow: 0 6px 25px rgba(255, 107, 107, 0.6); }
    100% { opacity: 0.95; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3); }
}

@keyframes gentlePulse {
    0% { opacity: 0.9; box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3); }
    50% { opacity: 1; box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4); }
    100% { opacity: 0.9; box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    25% { transform: translateY(-5px) rotate(-10deg); }
    50% { transform: translateY(-8px) rotate(0deg); }
    75% { transform: translateY(-3px) rotate(10deg); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px) rotate(-5deg); }
    75% { transform: translateX(3px) rotate(5deg); }
}

@keyframes slideUp {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* Hover Effects */
.top-performer-banner:hover,
.encouragement-banner:hover,
.warning-banner:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.top-performer-banner:hover {
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
}

.encouragement-banner:hover {
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
}

.warning-banner:hover {
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
}
</style>
<?php } ?>