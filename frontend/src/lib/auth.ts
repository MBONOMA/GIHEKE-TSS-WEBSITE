import Cookies from 'js-cookie';
import { jwtDecode } from 'jwt-decode';

interface JwtPayload {
  sub: string;
  email: string;
  role: string;
  iat: number;
  exp: number;
}

export function getToken(): string | undefined {
  return Cookies.get('token');
}

export function setToken(token: string): void {
  Cookies.set('token', token, { expires: 7, secure: false, sameSite: 'lax' });
}

export function removeToken(): void {
  Cookies.remove('token');
}

export function getUserRole(): string | null {
  const token = getToken();
  if (!token) return null;
  try {
    const decoded = jwtDecode<JwtPayload>(token);
    return decoded.role;
  } catch {
    return null;
  }
}

export function getUserEmail(): string | null {
  const token = getToken();
  if (!token) return null;
  try {
    const decoded = jwtDecode<JwtPayload>(token);
    return decoded.email;
  } catch {
    return null;
  }
}

export function isAuthenticated(): boolean {
  const token = getToken();
  if (!token) return false;
  try {
    const decoded = jwtDecode<JwtPayload>(token);
    return decoded.exp * 1000 > Date.now();
  } catch {
    return false;
  }
}

export function isAdmin(): boolean {
  const role = getUserRole();
  return role === 'super_admin' || role === 'admin' || role === 'staff';
}

export function isTeacher(): boolean {
  return getUserRole() === 'teacher';
}

export function isStudent(): boolean {
  return getUserRole() === 'student';
}

export function isParent(): boolean {
  return getUserRole() === 'parent';
}

export function canAccess(allowedRoles: string[]): boolean {
  const role = getUserRole();
  return role ? allowedRoles.includes(role) : false;
}
