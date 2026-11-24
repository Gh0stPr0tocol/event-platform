<?php

class AdminController
{
    public function __construct(
        private Event $events,
        private Category $categories,
        private Venue $venues,
        private Region $regions,
        private Registration $registrations
    ) {}

    public function dashboard(): void
    {
        requireAdmin();
        $eventStats=$this->events->statistics();
        $registrationStats=$this->registrations->dashboardStats();
        $busiest=$this->registrations->busiestUpcoming(8);
        $recent=$this->registrations->recentActivity(10);
        $statusCounts=[
            STATUS_CONFIRMED=>$this->registrations->listAll(['status'=>STATUS_CONFIRMED],1,1)['total'],
            STATUS_WAITLISTED=>$this->registrations->listAll(['status'=>STATUS_WAITLISTED],1,1)['total'],
            STATUS_CANCELLED=>$this->registrations->listAll(['status'=>STATUS_CANCELLED],1,1)['total'],
            STATUS_ATTENDED=>$this->registrations->listAll(['status'=>STATUS_ATTENDED],1,1)['total'],
        ];
        require APP_ROOT.'/app/views/admin/dashboard.php';
    }

    public function events(): void
    {
        requireAdmin();
        $filters=[
            'search'=>$this->queryString('search'),
            'category_id'=>$this->positiveQueryId($_GET['category_id']??''),
            'region_id'=>$this->positiveQueryId($_GET['region_id']??''),
            'time'=>in_array($this->queryString('time',20),['upcoming','past'],true)
                ? $this->queryString('time',20)
                : '',
        ];
        $pageParam = $_GET['p'] ?? '1';
        $page = is_string($pageParam)
            ? filter_var($pageParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;
        $page = $page === false ? 1 : $page;
        $result=$this->events->list($filters,$page,10);
        $page=$result['page'];
        $categories=$this->categories->all();$regions=$this->regions->all();
        require APP_ROOT.'/app/views/admin/events.php';
    }

    public function eventCreate(): void
    {
        requireAdmin();
        $event=null;$errors=[];$old=[];
        $categories=$this->categories->all();$venues=$this->venues->all();$regions=$this->regions->all();
        require APP_ROOT.'/app/views/admin/event_form.php';
    }

    public function eventStore(): void
    {
        requireAdmin();
        if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);redirect('?page=admin&action=events');}
        if(!verifyCsrfToken()){setFlash('error','Your session expired. Please try again.');redirect('?page=admin&action=eventCreate');}
        $this->saveEvent(null);
    }

    public function eventEdit(): void
    {
        requireAdmin();
        $id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        $event=$id?$this->events->find($id):null;
        if(!$event){http_response_code(404);require APP_ROOT.'/app/views/errors/404.php';return;}
        $errors=[];$old=$event;$categories=$this->categories->all();$venues=$this->venues->all();$regions=$this->regions->all();
        require APP_ROOT.'/app/views/admin/event_form.php';
    }

    public function eventUpdate(): void
    {
        requireAdmin();
        if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);redirect('?page=admin&action=events');}
        if(!verifyCsrfToken()){setFlash('error','Your session expired. Please try again.');redirect('?page=admin&action=events');}
        $id=filter_input(INPUT_POST,'event_id',FILTER_VALIDATE_INT);
        if(!$id){setFlash('error','Invalid event.');redirect('?page=admin&action=events');}
        $this->saveEvent($id);
    }

    private function saveEvent(?int $id): void
    {
        $current = $id !== null ? $this->events->find($id) : null;
        if ($id !== null && !$current) {
            setFlash('error', 'Event not found.');
            redirect('?page=admin&action=events');
        }
        $errors=[];$old=[
            'title'=>trim($_POST['title']??''),'description'=>trim($_POST['description']??''),
            'category_id'=>trim($_POST['category_id']??''),'venue_id'=>trim($_POST['venue_id']??''),
            'event_date'=>trim($_POST['event_date']??''),'start_time'=>trim($_POST['start_time']??''),
            'capacity'=>trim($_POST['capacity']??''),'image'=>$current['image']??null,
        ];
        foreach(['title'=>'Title','description'=>'Description','category_id'=>'Category','venue_id'=>'Venue','event_date'=>'Date','start_time'=>'Start time','capacity'=>'Capacity'] as $key=>$label){
            if($old[$key]==='')$errors[$key]=$label.' is required.';
        }
        validateLength($old['title'],'title',0,150,$errors);
        validateLength($old['description'],'description',0,5000,$errors);
        if($old['capacity']!==''&&!ctype_digit($old['capacity']))$errors['capacity']='Capacity must be a whole number.';
        elseif($old['capacity']!==''&&((int)$old['capacity']<1||(int)$old['capacity']>10000))$errors['capacity']='Capacity must be from 1 to 10000.';
        if($old['event_date']!==''){
            $date=DateTimeImmutable::createFromFormat('!Y-m-d',$old['event_date']);
            $dateErrors=DateTimeImmutable::getLastErrors();
            if(!$date||$date->format('Y-m-d')!==$old['event_date']||($dateErrors!==false&&($dateErrors['warning_count']>0||$dateErrors['error_count']>0)))$errors['event_date']='Enter a valid date.';
        }
        if($old['event_date']!==''&&!isset($errors['event_date'])&&$id===null&&$old['event_date']<date('Y-m-d'))$errors['event_date']='New events cannot be in the past.';
        if($old['start_time']!==''&&!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',$old['start_time']))$errors['start_time']='Enter a valid start time.';
        $categoryId=filter_var($old['category_id'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if($old['category_id']!==''&&($categoryId===false||!$this->categories->find($categoryId)))$errors['category_id']='Choose a valid category.';
        $venueId=filter_var($old['venue_id'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if($old['venue_id']!==''&&($venueId===false||!$this->venues->find($venueId)))$errors['venue_id']='Choose a valid venue.';
        if($id!==null){
            $confirmed=$this->events->confirmedCount($id);
            if($old['capacity']!==''&&(int)$old['capacity']<$confirmed)$errors['capacity']="Capacity cannot be below the {$confirmed} confirmed registrations.";
        }

        if(!empty($_FILES['image']['name'])){
            $file=$_FILES['image'];
            $allowed=[IMAGETYPE_JPEG=>'jpg',IMAGETYPE_PNG=>'png',IMAGETYPE_WEBP=>'webp'];
            $mime=@getimagesize($file['tmp_name']);
            $finfo=new finfo(FILEINFO_MIME_TYPE);
            $realMime=$file['tmp_name']!==''?$finfo->file($file['tmp_name']):'';
            $allowedMime=['image/jpeg','image/png','image/webp'];
            if(!is_uploaded_file($file['tmp_name']))$errors['image']='Invalid uploaded file.';
            elseif($file['size']>2*1024*1024)$errors['image']='Image must be 2 MB or smaller.';
            elseif(!$mime||!isset($allowed[$mime[2]])||!in_array($realMime,$allowedMime,true))$errors['image']='Only JPG, PNG and WebP images are allowed.';
            else{
                $extension=$allowed[$mime[2]];$filename=bin2hex(random_bytes(16)).'.'.$extension;
                $directory=APP_ROOT.'/public/images/events';
                if(!is_dir($directory))mkdir($directory,0775,true);
                if(!move_uploaded_file($file['tmp_name'],$directory.'/'.$filename))$errors['image']='We could not save the image.';
                else $old['image']=$filename;
            }
        }
        if(!$errors){
            try{
                $data=$old;$data['created_by']=(int)currentUser()['id'];
                if($id===null){$this->events->create($data);setFlash('success','Event created.');}
                else{$this->events->update($id,$data);setFlash('success','Event updated.');}
                redirect('?page=admin&action=events');
            }catch(Throwable $exception){error_log($exception->getMessage());$errors['general']='We could not save the event.';}
        }
        $event=$id?$this->events->find($id):null;$categories=$this->categories->all();$venues=$this->venues->all();$regions=$this->regions->all();
        require APP_ROOT.'/app/views/admin/event_form.php';
    }

    public function eventDelete(): void
    {
        requireAdmin();
        if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);redirect('?page=admin&action=events');}
        if(!verifyCsrfToken()){setFlash('error','Your session expired. Please try again.');redirect('?page=admin&action=events');}
        $id=filter_input(INPUT_POST,'event_id',FILTER_VALIDATE_INT);
        if(!$id){setFlash('error','Invalid event.');redirect('?page=admin&action=events');}
        try{$this->events->delete($id);setFlash('success','Event deleted.');}
        catch(Throwable $exception){error_log($exception->getMessage());setFlash('error','We could not delete this event.');}
        redirect('?page=admin&action=events');
    }

    public function categories(): void
    {
        requireAdmin();$page=max(1,(int)($_GET['page']??1));$categoryResult=$this->categories->list($page,10);
        $categories=$categoryResult['items'];$categoryTotal=$categoryResult['total'];$errors=[];$old=[];$editCategory=null;
        $editId=filter_input(INPUT_GET,'edit',FILTER_VALIDATE_INT);
        if($editId)$editCategory=$this->categories->find($editId);
        require APP_ROOT.'/app/views/admin/categories.php';
    }

    public function categoryStore(): void
    {
        requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=categories');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=categories');}
        $name=trim($_POST['name']??'');$errors=[];$editCategory=null;
        if($name==='')$errors['name']='Category name is required.';
        elseif(mb_strlen($name)>100)$errors['name']='Category name is too long.';
        elseif($this->categories->nameExists($name))$errors['name']='This category already exists.';
        if(!$errors){try{$this->categories->create($name);setFlash('success','Category added.');redirect('?page=admin&action=categories');}catch(Throwable $e){error_log($e->getMessage());$errors['general']='We could not add the category.';}}
        $categories=$this->categories->all();$old=['name'=>$name];require APP_ROOT.'/app/views/admin/categories.php';
    }

    public function categoryUpdate(): void
    {
        requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=categories');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=categories');}
        $id=filter_input(INPUT_POST,'category_id',FILTER_VALIDATE_INT);$name=trim($_POST['name']??'');$errors=[];
        if(!$id){setFlash('error','Invalid category.');redirect('?page=admin&action=categories');}
        if($name==='')$errors['name']='Category name is required.';
        elseif(mb_strlen($name)>100)$errors['name']='Category name is too long.';
        elseif($this->categories->nameExists($name,$id))$errors['name']='This category already exists.';
        if(!$errors){try{$this->categories->update($id,$name);setFlash('success','Category updated.');redirect('?page=admin&action=categories');}catch(Throwable $e){error_log($e->getMessage());$errors['general']='We could not update the category.';}}
        $categories=$this->categories->all();$editCategory=$this->categories->find($id);$old=['name'=>$name];require APP_ROOT.'/app/views/admin/categories.php';
    }

    public function categoryDelete(): void
    {
        requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=categories');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=categories');}
        $id=filter_input(INPUT_POST,'category_id',FILTER_VALIDATE_INT);
        try{$this->categories->delete($id);setFlash('success','Category deleted.');}
        catch(DomainException $exception){setFlash('error',$exception->getMessage());}
        catch(Throwable $exception){error_log($exception->getMessage());setFlash('error','We could not delete this category.');}
        redirect('?page=admin&action=categories');
    }

    public function venues(): void
    {
        requireAdmin();$page=max(1,(int)($_GET['page']??1));$venueResult=$this->venues->list($page,10);
        $venues=$venueResult['items'];$venueTotal=$venueResult['total'];$regions=$this->regions->all();$errors=[];$old=[];$editVenue=null;
        $editId=filter_input(INPUT_GET,'edit',FILTER_VALIDATE_INT);if($editId)$editVenue=$this->venues->find($editId);
        require APP_ROOT.'/app/views/admin/venues.php';
    }

    public function venueStore(): void
    {
        requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=venues');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=venues');}
        $data=['name'=>trim($_POST['name']??''),'address'=>trim($_POST['address']??''),'region_id'=>trim($_POST['region_id']??'')];
        $errors=[];$editVenue=null;
        foreach(['name'=>'Venue name','address'=>'Address','region_id'=>'County'] as $key=>$label)if($data[$key]==='')$errors[$key]=$label.' is required.';
        validateLength($data['name'],'name',0,150,$errors);
        validateLength($data['address'],'address',0,255,$errors);
        $regionId=filter_var($data['region_id'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if($data['region_id']!==''&&($regionId===false||!$this->regions->find($regionId)))$errors['region_id']='Choose a valid county.';
        if(!$errors){try{$this->venues->create($data);setFlash('success','Venue added.');redirect('?page=admin&action=venues');}catch(Throwable $e){error_log($e->getMessage());$errors['general']='We could not add the venue.';}}
        $venues=$this->venues->all();$regions=$this->regions->all();$old=$data;require APP_ROOT.'/app/views/admin/venues.php';
    }

    public function venueUpdate(): void
    {
        requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=venues');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=venues');}
        $id=filter_input(INPUT_POST,'venue_id',FILTER_VALIDATE_INT);
        $data=['name'=>trim($_POST['name']??''),'address'=>trim($_POST['address']??''),'region_id'=>trim($_POST['region_id']??'')];
        $errors=[];$editVenue=$id?$this->venues->find($id):null;
        if(!$id||!$editVenue){setFlash('error','Invalid venue.');redirect('?page=admin&action=venues');}
        foreach(['name'=>'Venue name','address'=>'Address','region_id'=>'County'] as $key=>$label)if($data[$key]==='')$errors[$key]=$label.' is required.';
        validateLength($data['name'],'name',0,150,$errors);
        validateLength($data['address'],'address',0,255,$errors);
        $regionId=filter_var($data['region_id'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if($data['region_id']!==''&&($regionId===false||!$this->regions->find($regionId)))$errors['region_id']='Choose a valid county.';
        if(!$errors&&$id){try{$this->venues->update($id,$data);setFlash('success','Venue updated.');redirect('?page=admin&action=venues');}catch(Throwable $e){error_log($e->getMessage());$errors['general']='We could not update the venue.';}}
        $venues=$this->venues->all();$regions=$this->regions->all();$old=$data;require APP_ROOT.'/app/views/admin/venues.php';
    }

    public function venueDelete(): void
    {
        requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=venues');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=venues');}
        $id=filter_input(INPUT_POST,'venue_id',FILTER_VALIDATE_INT);
        try{$this->venues->delete($id);setFlash('success','Venue deleted.');}
        catch(DomainException $exception){setFlash('error',$exception->getMessage());}
        catch(Throwable $exception){error_log($exception->getMessage());setFlash('error','We could not delete this venue.');}
        redirect('?page=admin&action=venues');
    }

    public function registrations(): void
    {
        requireAdmin();
        $status=$this->queryString('status',20);
        $filters=[
            'event_id'=>$this->positiveQueryId($_GET['event_id']??''),
            'status'=>in_array($status,REGISTRATION_STATUSES,true)?$status:'',
            'search'=>$this->queryString('search'),
        ];
        $page=max(1,(int)($_GET['page']??1));
        $result=$this->registrations->listAll($filters,$page,10);
        $eventList=$this->events->list([],1,1000)['items'];
        require APP_ROOT.'/app/views/admin/registrations.php';
    }

    private function queryString(string $key, int $maxLength = 200): string
    {
        $value=$_GET[$key]??'';
        return is_string($value)?substr(trim($value),0,$maxLength):'';
    }

    private function positiveQueryId(mixed $value): string
    {
        if(!is_string($value)&&!is_int($value))return '';
        $id=filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        return $id===false?'':(string)$id;
    }

    public function registrationStatus(): void
    {
        requireAdmin();
        if($_SERVER['REQUEST_METHOD']!=='POST')redirect('?page=admin&action=registrations');
        if(!verifyCsrfToken()){setFlash('error','Your session expired.');redirect('?page=admin&action=registrations');}
        $id=filter_input(INPUT_POST,'registration_id',FILTER_VALIDATE_INT);
        $status=(string)($_POST['status']??'');
        $allowed=REGISTRATION_STATUSES;
        if(!$id||!in_array($status,$allowed,true)){setFlash('error','Invalid registration status.');redirect('?page=admin&action=registrations');}
        try{$this->registrations->updateStatus($id,$status);setFlash('success','Registration status updated.');}
        catch(DomainException $exception){setFlash('error',$exception->getMessage());}
        catch(Throwable $exception){error_log($exception->getMessage());setFlash('error','We could not update this registration.');}
        redirect('?page=admin&action=registrations');
    }
}