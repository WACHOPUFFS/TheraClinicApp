import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RegisterExamplePage } from './register-example.page';

describe('RegisterExamplePage', () => {
  let component: RegisterExamplePage;
  let fixture: ComponentFixture<RegisterExamplePage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(RegisterExamplePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
