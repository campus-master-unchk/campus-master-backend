<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DevoirController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {

    /* -----------------------------------------------------------
     * ESPACE ADMIN 
     * ----------------------------------------------------------- */
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Gestion utilisateur
        Route::post('/users', [UserController::class, 'createUser']);
        Route::put('/users/{id}', [UserController::class, 'updateUser']);
        Route::get('/users', [UserController::class, 'getAll']);
        Route::patch('/users/{id}/status', [UserController::class, 'changeStatusUser']);
        // Départements
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
        // Niveau
        Route::post('/levels', [LevelController::class, 'createLevel']);
        Route::put('/levels/{id}', [LevelController::class, 'updateLevel']);
        Route::delete('/levels/{id}', [LevelController::class, 'deleteLevel']);
        // Specialites 
        Route::post('/specialities', [SpecialityController::class, 'createSpeciality']);
        Route::put('/specialities/{id}', [SpecialityController::class, 'updateSpeciality']);
        Route::delete('/specialities/{id}', [SpecialityController::class, 'deleteSpeciality']);
        // Modules
        Route::post('modules', [ModuleController::class, 'createModule']);
        Route::put('modules/{id}', [ModuleController::class, 'updateModule']);
        Route::delete('modules/{id}', [ModuleController::class, 'deleteModule']);
    });

    /* -----------------------------------------------------------
     * ESPACE ENSEIGNANT 
     * ----------------------------------------------------------- */
    Route::middleware('teacher')->prefix('teacher')->group(function () {
        // Gestion des Cours
        Route::get('/courses', [CourseController::class, 'myCourses']);
        Route::post('/courses', [CourseController::class, 'store']);
        Route::post('/courses/{id}', [CourseController::class, 'update']); // Utiliser POST pour l'upload de fichiers
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
        Route::patch('/courses/{id}/state', [CourseController::class, 'changeState']);

        // Gestion des Devoirs
        Route::get('/devoirs', [DevoirController::class, 'myDevoirs']);
        Route::post('/devoirs', [DevoirController::class, 'store']);
        Route::post('/devoirs/{id}', [DevoirController::class, 'update']); // POST pour les fichiers
        Route::delete('/devoirs/{id}', [DevoirController::class, 'destroy']);
        Route::patch('/devoirs/{id}/state', [DevoirController::class, 'changeState']);
        // Notes
        Route::post('/grades', [GradeController::class, 'createGrade']);
        // Devoir soumis
        Route::get('/devoirs/{devoirId}/submissions', [SubmissionController::class, 'teacherView']);
    });

    /* -----------------------------------------------------------
     * ESPACE ADMIN ET ENSEIGNANT 
     * ----------------------------------------------------------- */

    Route::middleware('admin-teacher')->group(function(){
        Route::post('/announcements', [AnnouncementController::class, 'createAnnouncement']);
        Route::put('/announcements/{id}', [AnnouncementController::class, 'updateAnnouncement']);
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroyAnnouncement']);
        Route::patch('/announcements/{id}/publish', [AnnouncementController::class, 'publish']);
    });

    /* -----------------------------------------------------------
     * ESPACE ÉTUDIANT 
     * ----------------------------------------------------------- */
    Route::middleware('student')->prefix('student')->group(function () {
        // Annonces
        Route::get('/announcements', [AnnouncementController::class, 'indexForStudent']);
        Route::get('/announcements/{id}', [AnnouncementController::class, 'showAnnouncement']);
        // Cours
        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/courses/{id}', [CourseController::class, 'show']);
        Route::get('/courses/{courseId}/devoirs', [DevoirController::class, 'indexByCourse']);
        // Notes
        Route::get('/grades', [GradeController::class, 'myGrades']);
        // Dépôt de devoir
        Route::post('/submissions', [SubmissionController::class, 'submitWork']);
        Route::get('/devoirs/{devoirId}/my-submissions', [SubmissionController::class, 'myHistory']);
    });

    /* -----------------------------------------------------------
     * ROUTES COMMUNES / GÉNÉRALES
     * ----------------------------------------------------------- */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/update-my-profile', [AuthController::class, 'updateMyProfile']);
    Route::put('update-my-password', [PasswordResetController::class, 'changePassword']);
    // Annonces
    Route::get('/announcements/general', [AnnouncementController::class, 'indexForGeneral']);
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/all', [NotificationController::class, 'getAllNotif']);
        Route::get('/unread', [NotificationController::class, 'getUnreadNotif']);
        Route::patch('/{id}/mark-read', [NotificationController::class, 'markRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
    });
    // Gestion Annonces 
    Route::get('/announcements', [AnnouncementController::class, 'indexForGeneral']);
    Route::get('/announcements/{id}', [AnnouncementController::class, 'showAnnouncement']);
    // Departements
    Route::get('/departments', [DepartmentController::class, 'index']);
    // Notes
    Route::get('/levels', [LevelController::class, 'getAllLevel']);
    // Specialites 
    Route::get('/specialities', [SpecialityController::class, 'getAllSpecialities']);
    // Modules
    Route::get('/modules', [ModuleController::class, 'getAllModules']);
});
