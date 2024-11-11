import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';
import { DataStorageService } from '../data-storage-service';
import { HttpClient } from '@angular/common/http';
import { ToastController, ModalController } from '@ionic/angular';
import { CookieService } from 'ngx-cookie-service';
import { SharedService } from '../shared.service'; // Importa el servicio compartido
import { LoadingController } from '@ionic/angular';
import { AuthService as Auth0Service } from '@auth0/auth0-angular';
import Chart from 'chart.js/auto';

import { ChangeDetectorRef } from '@angular/core';

@Component({
  selector: 'app-home',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
})
export class HomePage implements OnInit {


  appointmentsChart: any;
  newPatientsChart: any;
 

  periodTypes: any[] = []; // Para almacenar los tipos de periodos

  username: string;
  principalCompanies: { id: string, name: string, role: string, rfc: string, levelUser: string }[] = [];
  nonPrincipalCompanies: { id: string, name: string, role: string, rfc: string, status: string, levelUser: string }[] = [];
  selectedCompany: string;
  selectedRFC: string;
  selectedLevelUser: string;
  isFullScreen: boolean = false;
  currentLogo: string;
  currentAvatar: string;
  currentDateTime: string;

  selectedPeriod: any;


  totalAppointmentsByDay: number[] = [];
  newPatientsByDay: number[] = [];

  recentAppointments: any[] = []; // Citas más recientes
  totalAppointments: number = 0; // Total de citas del mes
  newPatients: number = 0; // Pacientes nuevos del mes
  usedSupplies: number = 0; // Insumos utilizados en la semana
  selectedDate: string = ''; // Fecha seleccionada en el calendario
  notifications: any[] = []; // Notificaciones recientes
  appointmentsThisWeek: number = 0; // Citas esta semana
  patientsThisWeek: number = 0; // Pacientes atendidos esta semana
  
  testimonials: any[] = []; // Testimonios de pacientes

  dailyTips: string[] = [
    "Recuerda mantener una postura correcta al sentarte para evitar lesiones de espalda.",
  ];

  dailyTip: string;

  constructor(
    private cookieService: CookieService,
    private toastController: ToastController,
    private modalController: ModalController,
    private http: HttpClient,
    private router: Router,
    public authService: AuthService,
    private dataStorageService: DataStorageService,
    public sharedService: SharedService, // Inyecta el servicio compartido
    private loadingController: LoadingController, // Inyecta el LoadingController
    private auth0Service: Auth0Service,
    private cdr: ChangeDetectorRef
  ) {

  }

  ngOnInit() {

    this.loadCurrentAvatar(this.authService.userId);
    const token = localStorage.getItem('token');

    if (!token) {
      this.router.navigate(['/login']);
    } else {
      if (!this.authService.isLoggedIn) {
        this.router.navigate(['/login']);
      } else {
        this.username = this.authService.username;
        this.selectedCompany = this.authService.selectedCompany;
        this.principalCompanies = this.authService.principalCompanies;
        this.nonPrincipalCompanies = this.authService.nonPrincipalCompanies;

        console.log('Username:', this.username);
        console.log('Principal Companies:', this.principalCompanies);
        console.log('Non-Principal Companies:', this.nonPrincipalCompanies);

        this.selectCompany();
        this.loadPermissions(); // Cargar permisos utilizando SharedService
        this.loadPeriodTypes(); // Cargar tipos de periodos disponibles



      }
    }

    this.loadSelectedPeriod();
    this.updateDateTime();


    this.auth0Service.user$.subscribe(user => {
      if (user) {
        this.currentAvatar = user.picture || 'ruta-a-imagen-default.jpg'; // URL del avatar
      }
    });
  }

  loadSelectedPeriod() {
    this.selectedPeriod = this.authService.selectedPeriod;  // Acceder al periodo seleccionado
  }

  async loadPermissions() {
    const loading = await this.presentLoading('Cargando permisos...');

    this.sharedService.loadPermissions().subscribe(
      (response: any) => {
        if (response.success) {
          this.sharedService.permissions = response.permissions.map((perm: any) => ({ section: perm.section, subSection: perm.subSection }));
          console.log('Permisos cargados:', this.sharedService.permissions); // Depuración
        } else {
          console.error(response.error);
        }
        loading.dismiss(); // Cierra el loading cuando termine la operación
      },
      (error) => {
        console.error('Error en la solicitud POST:', error);
        loading.dismiss(); // Cierra el loading si hay un error
      }
    );
  }

  hasPermission(section: string, subSection: string | null = null): boolean {
    return this.sharedService.hasPermission(section, subSection);
  }

  async loadPeriodTypes() {
    const loading = await this.presentLoading('Cargando tipos de periodos...');

    const companyId = this.authService.selectedId;
    this.authService.loadPeriodTypes(companyId).then((data) => {
      this.periodTypes = data;
      loading.dismiss(); // Cierra el loading si hay un error
    });
  }

  async onPeriodTypeChange(selectedType: any) {
    const loading = await this.presentLoading('Cargando tipos de periodos...');
    this.authService.setSelectedPeriod(selectedType);
    console.log('Tipo de periodo seleccionado:', selectedType);
    loading.dismiss();
  }


  async selectCompany() {
    const loading = await this.presentLoading('Cargando información...');

    if (this.selectedCompany) {
      const selectedPrincipalCompany = this.principalCompanies.find(company => company.name === this.selectedCompany);
      if (selectedPrincipalCompany) {
        this.authService.selectedId = selectedPrincipalCompany.id;
        this.authService.selectedRFC = selectedPrincipalCompany.rfc;
        this.authService.selectedLevelUser = selectedPrincipalCompany.levelUser;
        this.authService.selectedRole = selectedPrincipalCompany.role;

        console.log("ID de la empresa principal seleccionada:", selectedPrincipalCompany.id);

        this.companyNonPrincipals(selectedPrincipalCompany.id);
        this.authService.nonPrincipalCompanies = this.nonPrincipalCompanies.filter(company => company.id === selectedPrincipalCompany.id);
        console.log('Empresas no principales ligadas:', this.authService.nonPrincipalCompanies);
      }
    } else {
      if (this.principalCompanies.length > 0) {
        const firstCompany = this.principalCompanies[0];
        this.selectedCompany = firstCompany.name;
        this.authService.selectedId = firstCompany.id;
        this.authService.selectedRFC = firstCompany.rfc;
        this.authService.selectedLevelUser = firstCompany.levelUser;
        this.authService.selectedRole = firstCompany.role;

        console.log("ID de la primera empresa principal:", firstCompany.id);

        this.companyNonPrincipals(firstCompany.id);
        this.authService.nonPrincipalCompanies = this.nonPrincipalCompanies.filter(company => company.id === firstCompany.id);
        console.log('Empresas no principales ligadas:', this.authService.nonPrincipalCompanies);
      } else {
        this.authService.selectedRFC = '';
        this.authService.selectedRole = '';
        this.authService.selectedLevelUser = '';
        this.authService.nonPrincipalCompanies = [];
        console.log('Empresas no principales ligadas:', this.authService.nonPrincipalCompanies);
      }

      loading.dismiss(); // Cierra el loading cuando termine
    }

    this.authService.selectedCompany = this.selectedCompany;
    this.loadPermissions();
    this.onCompanyChange();

    this.loadRecentAppointments();
   
    this.loadStatistics();
    this.loadTestimonials();
    this.loadActivitySummary();

    console.log("Empresa seleccionada:", this.selectedCompany);
    console.log("RFC de la empresa seleccionada:", this.authService.selectedRFC);
    console.log("Nivel de usuario en la empresa seleccionada:", this.authService.selectedLevelUser);
    console.log("Nivel de rol en la empresa seleccionada:", this.authService.selectedRole);
  }

  async companyNonPrincipals(selectedCompanyId: string) {
    const loading = await this.presentLoading('Cargando empresas no principales...');

    const data = { company_id: selectedCompanyId };

    this.http.post('https://siinad.mx/php/loadCompanies.php', data).subscribe(
      async (response: any) => {
        if (response.success) {
          const mappedCompanies = response.nonPrincipalCompanies.map((company: any) => ({
            id: company.idAssociation,
            name: company.nameCompany,
            role: company.roleInCompany,
            rfc: company.rfcIncompany,
            status: company.statusCompany,
            levelUser: company.levelUser
          }));

          this.authService.setNonPrincipalCompanies(mappedCompanies);
          console.log('Empresas no principales obtenidas:', response.nonPrincipalCompanies);
        } else {
          await this.mostrarToast(response.message, 'danger');
        }
        loading.dismiss(); // Cierra el loading cuando termine la operación
      },
      (error) => {
        console.error('Error al realizar la solicitud:', error);
        this.mostrarToast('Error al realizar la solicitud', 'danger');
        loading.dismiss(); // Cierra el loading si hay un error
      }
    );
  }


  async mostrarToast(mensaje: string, color: string) {
    const toast = await this.toastController.create({
      message: mensaje,
      duration: 3000,
      color: color
    });
    toast.present();
  }

  noEmpresasAceptadas(): boolean {
    return this.authService.nonPrincipalCompanies.length === 0 || !this.authService.nonPrincipalCompanies.some(company => company.status === '1');
  }

  noEmpresasPendientes(): boolean {
    return this.authService.nonPrincipalCompanies.length === 0 || !this.authService.nonPrincipalCompanies.some(company => company.status === '0');
  }



  onCompanyChange() {
    this.loadCurrentLogo(this.authService.selectedId);
  }

  async loadCurrentLogo(companyId: string) {
    const loading = await this.presentLoading('Cargando logo de la empresa...');

    this.http.get(`https://siinad.mx/php/getCompanyLogo.php?companyId=${companyId}`).subscribe((response: any) => {
      this.currentLogo = response.logoUrl;
      loading.dismiss(); // Cierra el loading cuando termine la operación
    }, (error) => {
      console.error('Error al cargar el logo:', error);
      loading.dismiss(); // Cierra el loading si hay un error
    });
  }

  async loadCurrentAvatar(userId: string) {
    const loading = await this.presentLoading('Cargando avatar...');

    this.http.get(`https://siinad.mx/php/getUserAvatar.php?userId=${userId}`).subscribe(
      (response: any) => {
        if (response && response.avatarUrl) {
          this.currentAvatar = response.avatarUrl;
          console.log('Avatar URL:', this.currentAvatar);
        } else {
          console.error('Invalid response structure or missing avatarUrl:', response);
        }
        loading.dismiss(); // Cierra el loading cuando termine la operación
      },
      (error) => {
        console.error('Error fetching avatar:', error);
        loading.dismiss(); // Cierra el loading si hay un error
      }
    );
  }

  updateDateTime(): void {
    const now = new Date();
    const days = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    const dayName = days[now.getDay()];
    const day = now.getDate();
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();

    this.currentDateTime = `${dayName} ${day}, de ${monthName} ${year}`;
  }



  async presentLoading(message: string) {
    const loading = await this.loadingController.create({
      message: message,
      spinner: 'crescent',
      duration: 5000 // Opcional: Duración en milisegundos
    });
    await loading.present();
    return loading;
  }

  async logout() {
    const loading = await this.presentLoading('Cerrando sesión...');

    this.authService.logout();
    // Limpia los datos específicos de la aplicación
    localStorage.removeItem('userRole'); // u otros datos personalizados

    // Llama a logout de Auth0
    this.auth0Service.logout({
      logoutParams: { returnTo: window.location.origin } // Esta opción debería ser válida en la mayoría de las versiones recientes

    });
    loading.dismiss(); // Cierra el loading cuando termine la operación
  }


  // Cargar citas más recientes
  loadRecentAppointments() {
    const companyId = this.authService.selectedId; // Ajusta esta línea si tienes el ID de la empresa en otra parte
    const url = `https://siinad.mx/php/recent-appointment.php?company_id=${companyId}`;

    this.http.get<any>(url)
      .subscribe(response => {
        if (response.success) {
          this.recentAppointments = response.appointments || [];
        } else {
          console.error('Error en la respuesta al cargar citas recientes:', response.error);
        }
      }, error => {
        console.error('Error al cargar citas recientes:', error);
      });
  }



  // Cargar resumen de actividad
  // Cargar resumen de actividad
  loadActivitySummary() {
    const companyId = this.authService.selectedId;
  
    this.http.get<any>(`https://siinad.mx/php/activity-summary.php?company_id=${companyId}`)
      .subscribe(response => {
        if (response.success) {
          this.totalAppointmentsByDay = response.appointmentsByDay;
          this.newPatientsByDay = response.newEmployeesByDay;
          this.renderCharts();
        } else {
          console.error('Error en el resumen de actividad:', response.error);
        }
      }, error => {
        console.error('Error al cargar el resumen de actividad:', error);
      });
  }
  

renderCharts() {
  // Crear etiquetas de días (del 1 al 31) para el eje x
  const daysInMonth = Array.from({ length: 31 }, (_, i) => i + 1);

  // Gráfico para el total de citas diarias
  const appointmentsCtx = document.getElementById('appointmentsChart') as HTMLCanvasElement;
  if (this.appointmentsChart) {
    this.appointmentsChart.destroy();
  }
  this.appointmentsChart = new Chart(appointmentsCtx, {
    type: 'bar',
    data: {
      labels: daysInMonth, // Días del mes en el eje x
      datasets: [{
        label: 'Total de Citas',
        data: this.totalAppointmentsByDay, // Datos diarios de citas
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Citas'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Días del Mes'
          }
        }
      }
    }
  });

  // Gráfico para nuevos pacientes diarios
  const newPatientsCtx = document.getElementById('newPatientsChart') as HTMLCanvasElement;
  if (this.newPatientsChart) {
    this.newPatientsChart.destroy();
  }
  this.newPatientsChart = new Chart(newPatientsCtx, {
    type: 'bar',
    data: {
      labels: daysInMonth, // Días del mes en el eje x
      datasets: [{
        label: 'Nuevos Pacientes',
        data: this.newPatientsByDay, // Datos diarios de nuevos pacientes
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Pacientes'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Días del Mes'
          }
        }
      }
    }
  });
}




// Cargar estadísticas de la clínica
loadStatistics() {
  this.http.get<any>('https://siinad.mx/php/weekly-statistics.php?company_id=' + this.authService.selectedId)
    .subscribe(response => {
      this.appointmentsThisWeek = response.appointmentsThisWeek;
      this.patientsThisWeek = response.patientsThisWeek;
    }, error => {
      console.error('Error al cargar estadísticas de la clínica:', error);
    });
}



  // Cargar testimonios de pacientes
  loadTestimonials() {
    this.http.get<any>('https://example.com/api/testimonials')
      .subscribe(response => {
        this.testimonials = response.testimonials || [];
      }, error => {
        console.error('Error al cargar testimonios de pacientes:', error);
      });
  }



  // Cambiar de fecha en el calendario
  onDateChange(event: any) {
    this.selectedDate = event.detail.value;
  }

  getDailyTip() {
    const randomIndex = Math.floor(Math.random() * this.dailyTips.length);
    return this.dailyTips[randomIndex];
  }


}
