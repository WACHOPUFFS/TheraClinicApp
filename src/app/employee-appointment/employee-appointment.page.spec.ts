import { ComponentFixture, TestBed } from '@angular/core/testing';
import { EmployeeAppointmentPage } from './employee-appointment.page';

describe('EmployeeAppointmentPage', () => {
  let component: EmployeeAppointmentPage;
  let fixture: ComponentFixture<EmployeeAppointmentPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(EmployeeAppointmentPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
