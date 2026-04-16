<?php

namespace common\modules\auth\models;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property string|null $auth_fullname
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property User[] $users
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 */
class AuthItem extends \yii\db\ActiveRecord {

    CONST ROLE_Director = "director";
    CONST ROLE_HR_Senior = "hr1"; //	Human Resource Department
    CONST ROLE_InventoryCtrl = "invctrl"; //	Inventory Control
    CONST ROLE_Probation = "prob"; //	Probation Staffs
    CONST ROLE_PrdnFab_Executive = "prodfab"; //Production (Fabrication)
    CONST ROLE_PrdnFab_Wkr = "prodFabWorker"; //Production (Fabrication) Assignee
    CONST ROLE_PrdnElec_Executive = "prodelec"; //Production (Electrical)
    CONST ROLE_PrdnElec_Wkr = "prodElecWorker"; //	Production (Electrical) Assignee
    CONST ROLE_ProjCoordinator = "projcoor"; //Project Coordinator
    CONST ROLE_Staff = "staff"; //Normal Staff
    CONST ROLE_SystemAdmin = "sysadmin"; //System Admin
    CONST ROLE_FinanceExecutive = "finance1"; //System Admin
    CONST ROLE_Superior = "isSuperior"; //Superior

    /**
     * Bill of Material and Stock Outbound Module
     */
    CONST ROLE_Bom_Super = "bom_super";
    CONST ROLE_Bom_Normal = "bom_normal";
    CONST ROLE_Bom_View = "bom_view";
    CONST ROLE_Stock_Ob_Super = "stock_ob_super";
    CONST ROLE_Stock_Ob_Normal = "stock_ob_normal";
    CONST ROLE_Stock_Ob_View = "stock_ob_view";

    /**
     * New Auth Model
     */
    CONST Module_attendance = "attendance";
    CONST Module_CMMS = "cmms";

    /**
     * Employee Handbook Module
     */
    CONST ROLE_Eh_Super = "eh_super";
    CONST ROLE_Eh_Normal = "eh_normal";

    /**
     * Claim Module
     */
    CONST ROLE_CM_Normal = "claim_module_normal";
    CONST ROLE_CM_Superior = "claim_module_superior";
    CONST ROLE_CM_Finance = "claim_module_finance";

    /**
     * Claim Entitlement Module
     */
    CONST ROLE_CE_Superior = "claim_entitle_module_superior";
    CONST ROLE_CE_HR = "claim_entitle_module_hr";

    /**
     * Pre-Requisition Form Module
     */
    CONST ROLE_PRF_Superior = "prf_module_superior";
    CONST ROLE_PRF_Normal = "prf_module_normal";
    CONST ROLE_PRF_SuperUser = "prf_module_superuser";

    /**
     * production ot meal record Form Module
     */
    CONST ROLE_PROD_OT_MEAL_EXEC = "prod_ot_meal_record_exec";
    CONST ROLE_PROD_OT_MEAL_FINANCE = "prod_ot_meal_record_finance";

    /**
     * petty cash Form Module
     */
    CONST ROLE_PC_Normal = "petty_cash_normal";
    CONST ROLE_PC_Finance = "petty_cash_finance";

    /**
     * CMMS Module
     */
    CONST ROLE_CMMS_Normal = "cmms_normal";
    CONST ROLE_CMMS_Superior = "cmms_superior";
//    CONST ROLE_CMMS_SuperUser = "cmms_superuser";

    /**
     * Inventory Module
     */
    CONST ROLE_INVENTORY_Executive = "inventory_executive";
    CONST ROLE_INVENTORY_Assistant = "inventory_assistant";
    CONST ROLE_INVENTORY_ProjCoor = "inventory_projcoor";
    CONST ROLE_INVENTORY_MaintenanceHead = "inventory_maintenanceHead";
    CONST ROLE_INVENTORY_Personal = "inventory_personal";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['auth_fullname'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'name' => 'Name',
            'auth_fullname' => 'Auth Fullname',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments() {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('auth_assignment', ['item_name' => 'name']);
    }

    /**
     * Gets query for [[RuleName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName() {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * Gets query for [[AuthItemChildren]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren() {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * Gets query for [[AuthItemChildren0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0() {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren() {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * Gets query for [[Parents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParents() {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }
}
