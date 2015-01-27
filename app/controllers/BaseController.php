    <?php
/*
|--------------------------------------------------------------------------
| BaseController
|--------------------------------------------------------------------------
|
*/

class BaseController extends Controller
{
    /**
     * Initializer.
     *
     * @access   public
     * @return \BaseController
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));  
        $action = explode('@', Route::current()->getAction()['controller'])[1];
        Log::info($action);
        View::share('action', $action);
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }


    public function dashboard()
    {   
        $title  = Lang::get('Dashboard');

        return View::make('dashboard',   array(
                          'title'    => $title
                        ));

    }


    public function permissionDenied()
    {
        $title  = Lang::get('Access Denied');

        return View::make('denied',   array(
                          'title'    => $title
                        ));
    }
}