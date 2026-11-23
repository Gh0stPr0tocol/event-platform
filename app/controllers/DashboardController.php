<?php

class DashboardController
{
    public function __construct(
        private User $users,
        private Region $regions,
        private Registration $registrations,
        private Event $events
    ) {}

    public function index(): void
    {
        requireLogin();
        $user=$this->users->findById((int)currentUser()['id']);
        $upcoming=$this->registrations->findByUser((int)$user['id'],1,100,'upcoming');
        $past=$this->registrations->findByUser((int)$user['id'],1,100,'past');
        $stats=$this->registrations->statsForUser((int)$user['id']);
        $upcomingCount=$stats['upcoming'];
        $attendedCount=$stats['attended'];
        $cancelledCount=$stats['cancelled'];
        $next=null;
        foreach($upcoming['items'] as $registration) {
            if(in_array($registration['status'],[STATUS_CONFIRMED,STATUS_WAITLISTED],true)) {$next=$registration;break;}
        }
        $localEvents=$user['region_id']?$this->events->localUpcoming((int)$user['region_id'],(int)$user['id'],4):[];
        require APP_ROOT.'/app/views/dashboard/index.php';
    }

    public function registrations(): void
    {
        requireLogin();
        $tab=$_GET['tab']??'upcoming';
        if(!in_array($tab,['upcoming','past'],true))$tab='upcoming';
        $page=max(1,(int)($_GET['page']??1));
        $result=$this->registrations->findByUser((int)currentUser()['id'],$page,10,$tab);
        require APP_ROOT.'/app/views/dashboard/registrations.php';
    }

    public function profile(): void
    {
        requireLogin();
        $errors=[];$passwordErrors=[];
        $user=$this->users->findById((int)currentUser()['id']);
        $regions=$this->regions->all();
        require APP_ROOT.'/app/views/dashboard/profile.php';
    }

    public function updateProfile(): void
    {
        requireLogin();
        if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);redirect('?page=dashboard&action=profile');}
        if(!verifyCsrfToken()){setFlash('error','Your session expired. Please try again.');redirect('?page=dashboard&action=profile');}
        $data=[
            'name'=>trim($_POST['name']??''),'phone'=>trim($_POST['phone']??''),
            'region_id'=>trim($_POST['region_id']??'')
        ];
        $errors=[];
        validateRequired($data['name'],'name',$errors); validateRequired($data['phone'],'phone',$errors);
        validatePhone($data['phone'],$errors);
        validateRequired($data['region_id'],'region',$errors);
        if(!$errors){
            try{
                $this->users->updateProfile((int)currentUser()['id'],$data);
                $_SESSION['user']['name']=$data['name'];$_SESSION['user']['region_id']=$data['region_id'];
                setFlash('success','Profile updated.');
                redirect('?page=dashboard&action=profile');
            }catch(Throwable $exception){error_log($exception->getMessage());$errors['general']='We could not update your profile.';}
        }
        setOldInput($data);
        $user=$this->users->findById((int)currentUser()['id']);$regions=$this->regions->all();
        $passwordErrors=[];
        require APP_ROOT.'/app/views/dashboard/profile.php';
    }

    public function changePassword(): void
    {
        requireLogin();
        if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);redirect('?page=dashboard&action=profile');}
        if(!verifyCsrfToken()){setFlash('error','Your session expired. Please try again.');redirect('?page=dashboard&action=profile');}
        $passwordErrors=[];$current=(string)($_POST['current_password']??'');
        $new=(string)($_POST['new_password']??'');$confirmation=(string)($_POST['new_password_confirmation']??'');
        validateRequired($current,'current_password',$passwordErrors);validatePassword($new,$passwordErrors);
        if($new!==$confirmation)$passwordErrors['new_password_confirmation']='Passwords do not match.';
        if(!$passwordErrors){
            try{
                if(!$this->users->changePassword((int)currentUser()['id'],$current,$new)){
                    $passwordErrors['current_password']='Current password is incorrect.';
                }else{
                    setFlash('success','Password changed.');
                    redirect('?page=dashboard&action=profile');
                }
            }catch(Throwable $exception){error_log($exception->getMessage());$passwordErrors['general']='We could not change your password.';}
        }
        $errors=[];$user=$this->users->findById((int)currentUser()['id']);$regions=$this->regions->all();
        require APP_ROOT.'/app/views/dashboard/profile.php';
    }
}