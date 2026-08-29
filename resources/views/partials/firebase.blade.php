<!-- Firebase Direct Engine -->
<script>
  window.firebaseConfig = {
    apiKey: "{{ config('services.firebase.api_key') ?: 'AIzaSyCtxBnw5jv06A58wzB9WfNbY35O0XxNcNc' }}",
    authDomain: "{{ config('services.firebase.auth_domain') ?: 'pharmacymanagesystem.firebaseapp.com' }}",
    databaseURL: "{{ config('services.firebase.database_url') ?: 'https://pharmacymanagesystem-default-rtdb.firebaseio.com' }}",
    projectId: "{{ config('services.firebase.project_id') ?: 'pharmacymanagesystem' }}",
    storageBucket: "{{ config('services.firebase.storage_bucket') ?: 'pharmacymanagesystem.firebasestorage.app' }}",
    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') ?: '227901150233' }}",
    appId: "{{ config('services.firebase.app_id') ?: '1:227901150233:web:591a4ea18dc3b4e7d84688' }}",
    measurementId: "{{ config('services.firebase.measurement_id') ?: 'G-4G8B6Y9X28' }}"
  };

  function getFirebaseAuth() {
    if (typeof firebase === 'undefined') {
      throw new Error("Firebase SDK is still loading or blocked by browser extension. Please disable ad-blocker or refresh page.");
    }
    if (!firebase.apps.length) {
      firebase.initializeApp(window.firebaseConfig);
    }
    return firebase.auth();
  }

  async function syncUserWithServer(user) {
    const btn = document.getElementById('firebase-google-btn');
    if (btn) btn.innerHTML = '<span>Verifying account & logging in...</span>';

    const response = await fetch('/login/firebase', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        email: user.email,
        name: user.displayName,
        uid: user.uid
      })
    });

    const data = await response.json();
    if (data.success) {
      window.location.href = data.redirect || '/dashboard';
    } else {
      alert(data.message || 'Login verification failed on server.');
      resetGoogleButton();
    }
  }

  function resetGoogleButton() {
    const btn = document.getElementById('firebase-google-btn');
    if (btn) {
      btn.innerHTML = `
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Sign in with Google</span>
      `;
    }
  }

  window.loginWithFirebaseGoogle = async function() {
    const btn = document.getElementById('firebase-google-btn');
    if (btn) {
      btn.innerHTML = `
        <span class="inline-flex items-center gap-2 text-slate-700">
          <svg class="animate-spin h-4 w-4 text-[#008080]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          Connecting to Google...
        </span>
      `;
    }

    try {
      const auth = getFirebaseAuth();
      const provider = new firebase.auth.GoogleAuthProvider();
      provider.setCustomParameters({ prompt: 'select_account' });

      const result = await auth.signInWithPopup(provider);
      if (result && result.user) {
        await syncUserWithServer(result.user);
      } else {
        resetGoogleButton();
      }
    } catch (error) {
      console.error('Google Auth Error:', error);
      resetGoogleButton();

      if (error.code === 'auth/popup-blocked') {
        const auth = getFirebaseAuth();
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.setCustomParameters({ prompt: 'select_account' });
        auth.signInWithRedirect(provider);
      } else if (error.code === 'auth/popup-closed-by-user') {
        // Closed by user, no error alert needed
      } else if (error.code === 'auth/unauthorized-domain') {
        alert('⚠️ Firebase Authorized Domain Error:\nPlease wait 1-2 minutes for Firebase to sync domain.');
      } else {
        alert('Google Sign-In Alert: ' + (error.message || error.code || error));
      }
    }
  };

  // Check redirect result on load
  window.addEventListener('DOMContentLoaded', () => {
    try {
      if (typeof firebase !== 'undefined') {
        const auth = getFirebaseAuth();
        auth.getRedirectResult().then((res) => {
          if (res && res.user) {
            syncUserWithServer(res.user);
          }
        }).catch((e) => console.warn(e));
      }
    } catch (e) {
      console.warn(e);
    }
  });

  @auth
  const currentRole = "{{ auth()->user()->role }}";
  const userEmailHash = "{{ md5(auth()->user()->email) }}";
  let isUpdatingRole = false;

  function showRoleUpdatedToast(newRole) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-5 right-5 z-[9999] bg-slate-900 text-white px-5 py-4 rounded-xl shadow-2xl flex items-center space-x-3 border border-blue-500/50 animate-bounce';
    toast.innerHTML = `
      <div class="bg-blue-600 p-2 rounded-lg">
        <i class="fa-solid fa-user-shield text-white text-lg"></i>
      </div>
      <div>
        <h5 class="font-bold text-sm">Role Updated!</h5>
        <p class="text-xs text-slate-300">Your role has been changed to <span class="font-bold text-blue-400 uppercase">${newRole}</span>. Updating dashboard...</p>
      </div>
    `;
    document.body.appendChild(toast);
  }

  function handleRoleChange(newRole) {
    if (isUpdatingRole) return;
    if (newRole && newRole.toLowerCase() !== currentRole.toLowerCase()) {
      isUpdatingRole = true;
      showRoleUpdatedToast(newRole);
      setTimeout(() => {
        window.location.reload();
      }, 1200);
    }
  }

  try {
    if (typeof firebase !== 'undefined') {
      const db = firebase.database();
      db.ref('users/' + userEmailHash + '/role').on('value', (snapshot) => {
        const val = snapshot.val();
        if (val && val.toLowerCase() !== currentRole.toLowerCase()) {
          handleRoleChange(val);
        }
      });
    }
  } catch (e) {
    console.warn('Realtime listener error:', e);
  }
  @endauth
</script>
