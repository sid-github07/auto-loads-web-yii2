<?php

use common\models\City;
use common\models\Company;
use common\models\UserInvoice;
use common\models\UserService;
use yii\db\Migration;

/**
 * Class m161004_122035_user_invoice
 */
class m161004_122035_user_invoice extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(UserInvoice::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'user_service_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo paslaugos ID, kuriai išrašoma išankstinė sąskaita-faktūra'),
            'type' => $this->smallInteger(UserInvoice::MAX_TYPE_LENGTH)
                ->notNull()
                ->comment('Sąskaitos-faktūros tipas. Išankstinė/PVM'),
            'number' => $this->string()
                ->unique()
                ->notNull()
                ->comment('Unikalus sąskaitos-faktūros numeris'),
            'date' => $this->integer()
                ->notNull()
                ->comment('Sąskaitos-faktūros išrašymo data'),
            'seller_company_name' => $this->string()
                ->notNull()
                ->comment('Pardavėjo įmonės pavadinimas'),
            'seller_company_code' => $this->string()
                ->notNull()
                ->comment('Pardavėjo įmonės kodas'),
            'seller_vat_code' => $this->string()
                ->notNull()
                ->comment('Pardavėjo PVM kodas'),
            'seller_address' => $this->string()
                ->notNull()
                ->comment('Pardavėjo adresas'),
            'seller_bank_name' => $this->string()
                ->notNull()
                ->comment('Pardavėjo banko pavadinimas'),
            'seller_bank_code' => $this->string()
                ->notNull()
                ->comment('Pardavėjo banko kodas'),
            'seller_swift' => $this->string()
                ->notNull()
                ->comment('Pardavėjo banko SWIFT kodas'),
            'seller_bank_account' => $this->string()
                ->notNull()
                ->comment('Pardavėjo banko sąskaitos numeris'),
            'buyer_id' => $this->integer()
                ->notNull()
                ->comment('Pirkėjo įmonės ID'),
            'buyer_title' => $this->string()
                ->notNull()
                ->comment('Pirkėjo vardas ir pavardė arba įmonės pavadinimas'),
            'buyer_code' => $this->string()
                ->defaultValue(UserInvoice::DEFAULT_BUYER_CODE)
                ->comment('Pirkėjo asmens kodas arba įmonės kodas'),
            'buyer_vat_code' => $this->string()
                ->defaultValue(UserInvoice::DEFAULT_BUYER_VAT_CODE)
                ->comment('Pirkėjo PVM kodas'),
            'buyer_address' => $this->string()
                ->notNull()
                ->comment('Pirkėjo adresas'),
            'buyer_city_id' => $this->integer()
                ->notNull()
                ->comment('Pirkėjo miestas'),
            'buyer_phone' => $this->string()
                ->notNull()
                ->comment('Pirkėjo telefono numeris'),
            'buyer_email' => $this->string()
                ->notNull()
                ->comment('Pirkėjo el. paštas'),
            'product_name' => $this->string()
                ->notNull()
                ->comment('Prekės pavadinimas'),
            'netto_price' => $this->money(UserInvoice::NETTO_PRICE_PRECISION, UserInvoice::NETTO_PRICE_SCALE)
                ->notNull()
                ->comment('Prekės kaina'),
            'discount' => $this->money(UserInvoice::DISCOUNT_PRECISION, UserInvoice::DISCOUNT_SCALE)
                ->defaultValue(UserInvoice::DEFAULT_DISCOUNT)
                ->comment('Prekės nuolaida'),
            'vat' => $this->decimal(UserInvoice::VAT_PRECISION, UserInvoice::VAT_SCALE)
                ->defaultValue(UserInvoice::DEFAULT_VAT)
                ->comment('PVM procentais'),
            'days_to_pay' => $this->integer()
                ->defaultValue(UserInvoice::DEFAULT_DAYS_TO_PAY)
                ->comment('Dienų skaičius, per kiek reikia sumokėti'),
            'invoiced_by_position' => $this->string()
                ->notNull()
                ->comment('Išankstinę sąskaitą-faktūrą išrašiusio asmens pareigos'),
            'invoiced_by_name_surname' => $this->string()
                ->notNull()
                ->comment('Išankstinę sąskaitą-faktūrą išrašiusio asmens vardas ir pavardė'),
            'file_name' => $this->string()
                ->notNull()
                ->comment('Išankstinės sąskaitos-faktūros dokumento pavadinimas'),
            'file_extension' => $this->string()
                ->notNull()
                ->comment('Išankstinės sąskaitos-faktūros failo plėtinys'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'user_invoice_ibfk_1',
            UserInvoice::tableName(),
            'user_service_id',
            UserService::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_invoice_ibfk_2',
            UserInvoice::tableName(),
            'buyer_id',
            Company::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_invoice_ibfk_3',
            UserInvoice::tableName(),
            'buyer_city_id',
            City::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(UserInvoice::tableName());
    }
}
