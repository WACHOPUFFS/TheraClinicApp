import { ComponentFixture, TestBed } from '@angular/core/testing';
import { EmployeeControlPage } from './employee-control.page';

describe('EmployeeControlPage', () => {
  let component: EmployeeControlPage;
  let fixture: ComponentFixture<EmployeeControlPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(EmployeeControlPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
