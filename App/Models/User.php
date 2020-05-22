<?php

namespace App\Models;

use PDO;
use \App\Token;
use \Core\View;
use \App\Config;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users
                    VALUES (NULL, :name, :password_hash, :email)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			
			$results = $stmt->execute();
			
			$newUserId = $this->getNewUserId();	
			$this->addUserIncomes($newUserId);
			$this->addUserExpenses($newUserId);
			$this->addUserPaymentMethods($newUserId);

            return $results;
        }

        return false;
    }
	
	protected function addUserIncomes($id)
	{
		$db = static::getDB();
		$doDeafultIncomeCategoriesExist = $db->query('SELECT * FROM incomes_categories WHERE id=1');
		
		if($doDeafultIncomeCategoriesExist->rowCount() == 0) {
			$addDeafultIncomeCategories = $db->query("INSERT INTO incomes_categories VALUES (NULL,'Wynagrodzenie'), (NULL,'Odsetki bankowe'), (NULL,'Sprzedaż internetowa'), (NULL,'Inne')");
		}
		
		$addDefaultUserIncomeCategories = $db->query("INSERT INTO incomes_categories_assigned_to_users VALUES (NULL,'$id','Wynagrodzenie'), (NULL,'$id','Odsetki bankowe'), (NULL,'$id','Sprzedaż internetowa'), (NULL,'$id','Inne')");	
	}	
	
	protected function addUserExpenses($id)
	{
		$db = static::getDB();
		$doDeafultExpenseCategoriesExist = $db->query('SELECT * FROM expenses_categories WHERE id=1');
		
		if($doDeafultExpenseCategoriesExist->rowCount() == 0) {
			$addDeafultExpenseCategories = $db->query("INSERT INTO expenses_categories VALUES (NULL,'Jedzenie'),(NULL,'Mieszkanie'),(NULL,'Transport'), (NULL,'Telekomunikacja'),(NULL,'Opieka zdrowotna'),(NULL,'Ubrania'),(NULL,'Rozrywka'),(NULL,'Podróże'),(NULL,'Książki'),(NULL,'Oszczędności'),(NULL,'Spłata długu'),(NULL,'Inne')");
		}
		
		$addDefaultUserExpenseCategories = $db->query("INSERT INTO expenses_categories_assigned_to_users VALUES (NULL,'$id','Jedzenie'),(NULL,'$id','Mieszkanie'),(NULL,'$id','Transport'), (NULL,'$id','Telekomunikacja'),(NULL,'$id','Opieka zdrowotna'),(NULL,'$id','Ubrania'),(NULL,'$id','Rozrywka'),(NULL,'$id','Podróże'),(NULL,'$id','Książki'),(NULL,'$id','Oszczędności'),(NULL,'$id','Spłata długu'),(NULL,'$id','Inne')");	
	}	
	
	protected function addUserPaymentMethods($id)
	{
		$db = static::getDB();
		$doDeafultPaymentMethodsExist = $db->query('SELECT * FROM payment_methods WHERE id=1');
		
		if($doDeafultPaymentMethodsExist->rowCount() == 0) {
			$addDeafultPaymentMethods = $db->query("INSERT INTO payment_methods VALUES (NULL,'Gotówka'), (NULL,'Karta debetowa'), (NULL,'Karta kredytowa')");
		}
		
		$addDefaultUserPaymentMethods = $db->query("INSERT INTO payment_methods_assigned_to_users VALUES (NULL,'$id','Gotówka'), (NULL,'$id','Karta debetowa'),(NULL,'$id','Karta kredytowa')");	
	}
	
	protected function getNewUserId()
	
	{	$db = static::getDB();
		$newUserIdQuery = $db->query("SELECT id FROM users WHERE email='$this->email'")->fetch();
		$newUserId = $newUserIdQuery['id'];

		return $newUserId;
				
	}


    public function validate()
    {

		$this->validateNameAndEmail();
        // Password
		if (isset($this->password)) {
			
			if (preg_match('/(?=.*?[0-9])(?=.*?[A-Za-z]).+/', $this->password) == 0) {
				$this->errors['password'] = 'Hasło musi posiadać przynajmniej 1 literę i 1 cyfrę.';
			}
			
			if (strlen($this->password) < 6 || strlen($this->password) > 20) {
				$this->errors['password'] = 'Hasło musi posiadać od 6 do 20 znaków.';
			}

		}
		
		$resposneCaptcha = $this->validateCaptcha();
		if(!($resposneCaptcha->success))
		{
			$validation_successful = false;
			$this->errors['captcha'] = "Potwierdź, że nie jesteś botem.";
		}
		
    }
	
	protected function validateNameAndEmail() 
	{
        // Name
        if ($this->name == '') {
            $this->errors['name'] = 'Wprowadź imię.';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['email'] = 'Podaj poprawny adres e-mail.';
        }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors['email'] = 'Istnieje konto o podanym adresie e-mail.';
        }		
	}
	
	protected function validateCaptcha()
	{
		
		$checkCaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.Config::CAPTCHA_SECRET.'&response='.$_POST['g-recaptcha-response']);
		
		return json_decode($checkCaptcha);

	}

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    
    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }
		
        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }   
	
	public function updateProfile()
    {
        $this->validateNameAndEmail();
		
		var_dump($this->errors);

        if (empty($this->errors)) {
			

			$sql = 'UPDATE users SET name = :name, email = :email WHERE id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			
			$results = $stmt->execute();

            return $results;
        }

        return false;
    }	
	
	public function deleteAccount()
    {
        	$sql = 'DELETE FROM users WHERE id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			
			$stmt->execute();
			
    }


	
	
	
	
	
}
