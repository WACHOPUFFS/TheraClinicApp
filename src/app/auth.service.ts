import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  isLoggedIn = false;
  username: string;
  userId: string;
  avatar: string;
  principalCompanies: { id: string, name: string, role: string, rfc: string, levelUser: string }[] = [];
  nonPrincipalCompanies: { id: string, name: string, role: string, rfc: string, status: string, levelUser: string }[] = [];
  selectedId: string = '';
  selectedCompany: string = '';
  selectedRFC: string = '';
  selectedLevelUser: string = '';
  selectedRole: string = '';

  selectedPeriod: any = null;
  periodTypes: any[] = []; // Para almacenar los tipos de periodos disponibles

  constructor(private http: HttpClient) {
    // Verificar el estado de la sesión al cargar la aplicación
    this.isLoggedIn = this.checkAuthStatus();
    if (this.isLoggedIn) {
      this.username = localStorage.getItem('username') || '';
      this.userId = localStorage.getItem('userId') || '';
      this.loadMappedPrincipalCompanies();
      this.loadSelectedPeriod(); // Cargar el periodo seleccionado si existe
    }
  }

  // Método para cargar los tipos de periodos desde la base de datos
  loadPeriodTypes(companyId: string) {
    return this.http.get(`https://siinad.mx/php/get_period_types.php?company_id=${companyId}`)
      .toPromise()
      .then((data: any) => {
        this.periodTypes = data;
        return data;
      })
      .catch(error => {
        console.error('Error al cargar los tipos de periodos', error);
      });
  }

  // Método para guardar el periodo seleccionado en localStorage
  setSelectedPeriod(period: any) {
    this.selectedPeriod = period;
    localStorage.setItem('selectedPeriod', JSON.stringify(period));
  }

  loadSelectedPeriod() {
    const periodString = localStorage.getItem('selectedPeriod');
    if (periodString) {
      this.selectedPeriod = JSON.parse(periodString);
      console.log('Periodo seleccionado cargado desde localStorage:', this.selectedPeriod);
    }
  }

  login(username: string, userId: string, avatar: string, principalCompanies: any[]) {
    this.isLoggedIn = true;
    this.username = username;
    this.userId = userId;
    this.avatar = avatar;

    // Guarda el estado de la sesión en localStorage
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('username', username);
    localStorage.setItem('userId', userId);

    // Mapear las empresas principales
    this.principalCompanies = principalCompanies.map(company => ({
      id: company.id,
      name: company.name,
      role: company.role,
      rfc: company.rfc,
      levelUser: company.levelUser
    }));

    // Guardar las empresas principales en localStorage
    localStorage.setItem('mappedPrincipalCompanies', JSON.stringify(this.principalCompanies));

    // Marcar que el usuario ya ha iniciado sesión y no es la primera vez
    this.setFirstTimeComplete();

    console.log('Empresas principales almacenadas:', this.principalCompanies);
    console.log('Empresas no principales almacenadas:', this.nonPrincipalCompanies);
  }

  setNonPrincipalCompanies(companies: any[]) {
    this.nonPrincipalCompanies = companies.map(company => ({
      id: company.id,
      name: company.name,
      role: company.role,
      rfc: company.rfc,
      status: company.status,
      levelUser: company.levelUser
    }));
    console.log('Empresas no principales almacenadas:', this.nonPrincipalCompanies);
  }

  checkAuthStatus(): boolean {
    const isLoggedIn = localStorage.getItem('isLoggedIn');
    return isLoggedIn === 'true';
  }

  logout() {
    this.isLoggedIn = false;
    this.username = '';
    this.userId = '';
    this.principalCompanies = [];
    this.nonPrincipalCompanies = [];
    this.selectedCompany = '';
    this.selectedRFC = '';
    this.selectedLevelUser = '';
    this.selectedRole = '';

    // Elimina los datos de localStorage al cerrar sesión
    localStorage.clear();

    console.log('Empresas principales limpiadas:', this.principalCompanies);
    console.log('Empresas no principales limpiadas:', this.nonPrincipalCompanies);
    console.log('Empresa seleccionada limpia:', this.selectedCompany);
  }

  // Método para cargar las empresas principales mapeadas guardadas en localStorage
  loadMappedPrincipalCompanies() {
    const mappedPrincipalCompaniesString = localStorage.getItem('mappedPrincipalCompanies');
    if (mappedPrincipalCompaniesString) {
      const mappedPrincipalCompanies = JSON.parse(mappedPrincipalCompaniesString);
      this.principalCompanies = mappedPrincipalCompanies;
      console.log('Empresas principales cargadas desde localStorage:', this.principalCompanies);
    }
  }

  // Método para verificar si es la primera vez que se inicia sesión
  isFirstTime(): boolean {
    const isFirstTime = localStorage.getItem('isFirstTime');
    return isFirstTime !== 'false';
  }

  // Método para marcar que el usuario ya ha iniciado sesión por primera vez
  setFirstTimeComplete(): void {
    localStorage.setItem('isFirstTime', 'false');
  }
}
